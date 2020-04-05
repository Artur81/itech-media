<?php

namespace App\Http\Controllers;

use App\Services\Api\TwitterService;
use Illuminate\Routing\Controller as BaseController;

class PostController extends BaseController
{
    protected $twitterService;

    const SCREEN_NAME = 'twitterapi';
    const COUNT = 5;

    public function __construct(TwitterService $twitterService)
    {
        $this->twitterService = $twitterService;
    }

    public function getTwitterPosts()
    {
        $query = [
            'screen_name' => self::SCREEN_NAME,
            'count' => self::COUNT
        ];

        $posts = $this->twitterService->request(TwitterService::METHOD_GET, TwitterService::PATH_GET_TIMELINE, $query);

        return view('post.get_twitter')->with(['posts' => $posts]);
    }
}
