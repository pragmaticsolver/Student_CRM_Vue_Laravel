<?php

namespace App\Jobs;

use App\Helpers\LineHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendLineMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $lineUserId;
    private $messageBuilder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lineUserId, $messageBuilder)
    {
        $this->lineUserId = $lineUserId;
        $this->messageBuilder = $messageBuilder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $res = LineHelper::getBot();
        if($res['status'] == 0) 
        {
            \Log::error($res['message']);
            return;
        }
        
        $bot = $res['bot'];
        $response = $bot->pushMessage($this->lineUserId, $this->messageBuilder);
        
        if(!$response->isSucceeded())
        {
            \Log::error($response->getJSONDecodedBody());
        }
    }
}
