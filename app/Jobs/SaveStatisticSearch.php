<?php

namespace App\Jobs;

use App\Models\StatisticSearch;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveStatisticSearch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $request;

    protected User|bool $user;

    public function __construct(string $request, User|bool|null $user)
    {
        $this->request = $request;
        $this->user = $user;
        $this->onQueue('low');
    }

    public function handle(): void
    {
        StatisticSearch::create([
            'user_id' => $this->user->id ?? null,
            'query' => $this->request,
        ]);
    }
}
