<?php

namespace App\Jobs\PerformanceRelevance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserPerformanceStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $userId;
    public $articleId;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $articleId)
    {
        $this->userId = $userId;
        $this->articleId = $articleId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new \App\Helpers\Relevance\UserPerformanceStoreHelper)->start()
        ->articleId($this->articleId)
        ->userId($this->userId)
        ->send();

        return true;
    }
}
