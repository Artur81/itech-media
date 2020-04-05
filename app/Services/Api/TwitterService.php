<?php
namespace App\Services\Api;

class TwitterService extends ApiService
{
    const PATH_GET_TIMELINE = '/1.1/statuses/user_timeline.json';

    protected function getConfig(): void
    {
        $this->token = env('TWITTER_API_TOKEN');
        $this->tokenSecret = env('TWITTER_API_TOKEN_SECRET');
        $this->consumerKey = env('TWITTER_API_CONSUMER_KEY');
        $this->consumerSecret = env('TWITTER_API_CONSUMER_SECRET');
        $this->host = env('TWITTER_API_HOST');
    }
}
