<?php

namespace App\Criteria;

use App\CriteriaInterface;
use App\Participant;

class RandomUserPick implements CriteriaInterface
{
    public function pick(array $entries, array $participants): Participant
    {
        $id = array_rand($participants);
        $pick = $participants[$id];
        $winner = new Participant();

        $winner->username = $pick['username'];
        $winner->name = $pick['name'];
        $winner->user_id = $id;

        return $winner;
    }
}