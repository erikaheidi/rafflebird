<?php


namespace App\Command\Pick;

use App\Criteria\RandomUserPick;
use App\Participant;
use Minicli\Command\CommandController;
use Minicli\Minicache\FileCache;

class RandomController extends CommandController
{
    public function handle()
    {
        if (!$this->hasParam('id')) {
            $this->getPrinter()->error("You must provide id=TWEET_ID");
            return 1;
        }

        $tweet_id = $this->getParam('id');
        //generate json cache of participants
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
        $criteria = $this->getApp()->config->raffle_criteria ?? new RandomUserPick();

        /** @var Participant $winner */
        $winner = $criteria->pick($entries,$participants);

        $this->getPrinter()->success("WE HAVE A WINNER!!! ", true);
        $this->getPrinter()->success('@' . $winner->username . ' - ' . $winner->name);
        $this->getPrinter()->info("Visit their profile at: https://twitter.com/" . $winner->username);

        return 0;
    }
}