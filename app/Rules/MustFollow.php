<?php


namespace App\Rules;


use Abraham\TwitterOAuth\TwitterOAuth;
use App\RuleInterface;
use Minicli\App;

class MustFollow implements RuleInterface
{
    protected string $raffle_admin;

    public function __construct(App $app)
    {
        $this->raffle_admin = $app->config->raffle_admin;
    }

    public function validate(TwitterOAuth $client, string $author_username, array $tweet): bool
    {
        $content = $client->get("friendships/show", [
            "source_screen_name" => $author_username,
            "target_screen_name" => $this->raffle_admin
        ]);

        return $content->relationship->source->following;
    }
}