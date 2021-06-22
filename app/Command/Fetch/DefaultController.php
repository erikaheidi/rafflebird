<?php

namespace App\Command\Fetch;

use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle()
    {
        $this->getPrinter()->newline();
        $this->getPrinter()->out("Usage:");
        $this->getPrinter()->info("./rafflebird fetch replies id=[TWEET_ID]");
        $this->getPrinter()->newline();
    }
}