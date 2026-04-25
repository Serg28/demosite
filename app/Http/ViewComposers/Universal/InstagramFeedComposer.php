<?php

namespace App\Http\ViewComposers\Universal;

use Dymantic\InstagramFeed\Profile;
use Illuminate\View\View;

class InstagramFeedComposer
{
    private Profile $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function compose(View $view): void
    {
        $profileInsta = $this->profile::where('username', setting('user_instagram_feeds'))->first();
        $feed = $profileInsta ? $profileInsta->feed(setting('count_instagram_feeds')) : false;

        $view->with(compact('feed'));
    }
}
