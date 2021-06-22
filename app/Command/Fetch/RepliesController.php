<?php


namespace App\Command\Fetch;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\RuleInterface;
use Minicli\Command\CommandController;
use App\TwitterV2Client;
use Minicli\Minicache\FileCache;

class RepliesController extends CommandController
{
    public function handle()
    {
        if (!$this->hasParam('id')) {
            $this->getPrinter()->error("You must provide id=TWEET_ID");
            return 1;
        }

        $tweet_id = $this->getParam('id');
        $this->getPrinter()->info("Obtaining replies for tweet ID $tweet_id...");

        $twitter_bearer_token = $this->getApp()->config->twitter_bearer_token;
        $client = new TwitterV2Client($twitter_bearer_token);

        $participants = [];
        $ignore_usernames = $this->getApp()->config->ignore_usernames ?? [];
        $ignore_usernames[] = $this->getApp()->config->raffle_admin;

        $table[] = [
            'USERNAME',
            'REPLY LINK',
            'EXCERPT',
        ];

        $max_iterations = 10;
        $next_token = null;
        $all_entries = [];

        for ($i = 1 ; $i < $max_iterations; $i++) {
            $replies = $client->getThreadReplies($tweet_id, 100, $next_token);

            if (!$replies) {
                $this->getPrinter()->error("An error occurred.");
                return 1;
            }

            if (!isset($replies['data'])) {
                $this->getPrinter()->error("No data found. **Keep in mind that tweets older than 1 week are not available via the recent search endpoint**");
                return 1;
            }

            $tweets = $replies['data'];
            $users = [];
            foreach ($replies['includes']['users'] as $user) {
                $users[$user['id']] = ['username' => $user['username'], 'name' => $user['name']];
            };

            foreach ($tweets as $reply) {
                $author = $users[$reply['author_id']];
                if (in_array($author['username'], $ignore_usernames)) {
                    continue;
                }

                $twitterV1Client = new TwitterOAuth(
                    $this->getApp()->config->twitter_api_key,
                    $this->getApp()->config->twitter_api_secret,
                    $this->getApp()->config->twitter_access_token,
                    $this->getApp()->config->twitter_token_secret
                );

                //validate participation
                foreach ($this->getApp()->config->raffle_rules as $rule_class) {
                    /** @var RuleInterface $rule */
                    $rule = new $rule_class($this->getApp());
                    if ($rule->validate($twitterV1Client, $author['username'], $reply)) {
                        $table[] = [
                            $author['username'],
                            "https://twitter.com/" . $author['username'] . "/status/" . $reply['id'],
                            substr($reply['text'], 0, 30) . '...',
                        ];

                        $participants[$reply['author_id']] = $author;
                        $all_entries[] = $reply;
                    }
                };
            }

            if (!isset($replies['meta']['next_token'])) {
                break;
            }

            $next_token = $replies['meta']['next_token'];
        }

        $this->getPrinter()->info("Total Participating: " . count($participants));
        if (!$this->hasFlag('s')) {
            $this->getPrinter()->printTable($table);
        }

        if ($this->hasFlag('save')) {
            $cache = new FileCache(__DIR__ . '/../../../var/cache');
            $cache->save(json_encode($participants), $tweet_id . '-participants');
            $cache->save(json_encode($all_entries), $tweet_id . '-entries');
        }

        return 0;
    }
}