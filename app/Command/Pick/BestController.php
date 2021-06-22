<?php


namespace App\Command\Pick;

use App\Criteria\BestPick;
use App\Criteria\RandomUserPick;
use App\Participant;
use Minicli\Command\CommandController;
use Minicli\Minicache\FileCache;

class BestController extends CommandController
{
    public function handle()
    {
        if (!$this->hasParam('id')) {
            $this->getPrinter()->error("You must provide id=TWEET_ID");
            return 1;
        }

        $tweet_id = $this->getParam('id');

        $this->getApp()->runCommand([
            'rafflebird',
            'fetch',
            'replies',
            'id=' . $tweet_id,
            '--save',
            $this->hasFlag('s') ? '--s' : ''
        ]);

        $cache = new FileCache(__DIR__ . '/../../../var/cache');
        $entries = json_decode($cache->getCached($tweet_id . '-entries'), true);
        $participants = json_decode($cache->getCached($tweet_id . '-participants'), true);

        //pick a winner according with established criteria
        $criteria = new BestPick($this->getApp()->config->ignore_usernames);
        $winner = $criteria->pick($entries,$participants);

        $this->getPrinter()->success("WE HAVE A WINNER!!! ", true);
        $this->getPrinter()->success('@' . $winner->username . ' - ' . $winner->name);
        $this->getPrinter()->success(sprintf("Most Liked Tweet, with %s likes: ", $winner->entry['public_metrics']['like_count']), true);
        $this->getPrinter()->info($winner->entry['text']);

        $this->getPrinter()->success("Reply Link: https://twitter.com/" . $winner->username . "/status/" . $winner->entry['id'], true);
        $this->getPrinter()->success("Visit their profile at: https://twitter.com/" . $winner->username);

        return 0;
    }
}