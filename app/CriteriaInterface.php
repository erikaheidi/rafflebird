<?php

namespace App;

interface CriteriaInterface
{
    public function pick(array $entries, array $participants): Participant;
}