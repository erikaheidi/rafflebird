<?php

namespace App;

use Abraham\TwitterOAuth\TwitterOAuth;
use Minicli\App;

interface RuleInterface
{
    public function __construct(App $app);

    public function validate(TwitterOAuth $client, string $author_username, array $tweet): bool;
}