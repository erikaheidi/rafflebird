<?php

namespace App\Command\Pick;

use Minicli\Command\CommandController;

class DefaultController extends CommandController
{
    public function handle()
    {
        $this->getPrinter()->newline();
        $this->getPrinter()->out("Usage:");
        $this->getPrinter()->info("./rafflebird pick random|best id=[TWEET_ID]");
        $this->getPrinter()->out("- random: picks a random reply\n");
        $this->getPrinter()->out("- best: picks the reply with most likes.\n");
        $this->getPrinter()->newline();
    }
}