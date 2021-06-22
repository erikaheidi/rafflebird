<?php

namespace App\Criteria;

use App\CriteriaInterface;
use App\Participant;

class BestPick implements CriteriaInterface
{
    public array $ignore_users = [];

    public function __construct($ignore_users = [])
    {
        $this->ignore_users = $ignore_users;
    }

    public function pick(array $entries, array $participants): Participant
    {
        $ranking = [];

        foreach ($entries as $entry) {
            //user must be in participants to be a valid entry
            $author_id = $entry['author_id'];
            if (!isset($participants[$author_id])) {
                continue;
            }

            $likes = $entry['public_metrics']['like_count'];
            $ranking[$likes] = $entry;
        }

        ksort($ranking);
        $pick = array_pop($ranking);

        $winner = new Participant();
        $winner->user_id = $pick['author_id'];
        $winner->username = $participants[$winner->user_id]['username'];
        $winner->name = $participants[$winner->user_id]['name'];
        $winner->entry = $pick;

        return $winner;
    }
}