<?php

namespace App;

use Minicli\Curly\Client;

class TwitterV2Client
{
    protected Client $client;
    protected string $bearer_token;

    static string $API_ENDPOINT = 'https://api.twitter.com/2';

    public function __construct(string $bearer_token)
    {
        $this->bearer_token = $bearer_token;
        $this->client = new Client();
    }

    public function getThreadReplies($tweet_id, $limit = 100, $cursor = null): array
    {
        $fields = "author_id,conversation_id,created_at,public_metrics";
        $expansions = "author_id";
        $user_fields = "name,username";
        $query = sprintf(
            "/tweets/search/recent?query=conversation_id:%s&tweet.fields=%s&expansions=%s&user.fields=%s&max_results=%s%s",
            $tweet_id,
            $fields,
            $expansions,
            $user_fields,
            $limit,
            $cursor ? '&next_token='.$cursor : ''
        );

        $response = $this->query($query);

        if ($response['code'] == 200) {
            return json_decode($response['body'], true);
        }

        return $response;
    }

    public function query($query): array
    {
        return $this->client->get(self::$API_ENDPOINT . $query, $this->getHeaders());
    }

    public function getHeaders(): array
    {
        return [
            "Authorization: Bearer $this->bearer_token"
        ];
    }
}