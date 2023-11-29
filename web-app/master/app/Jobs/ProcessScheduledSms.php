<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Semaphore\SemaphoreClient;
use App\Models\Booking\SmsLogs;
use App\Models\Booking\SmsJobHistory;
use Carbon\Carbon;

class ProcessScheduledSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;
    protected $recepient;
    protected $sendingType;
    protected $message;
    protected $subject;
    protected $eventId;
    protected $sendingDate;

    private $options = ['sendername' => 'camayacoast', 'apiBase' => 'https://api.semaphore.co/api/v4/messages'];

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    public function __construct(
        $userId, 
        $recepient, 
        $sendingType, 
        $message, 
        $eventId, 
        $sendingDate
    )
    {
        $this->userId = $userId;
        $this->recepient = $recepient;
        $this->sendingType = $sendingType;
        $this->message = $message;
        $this->eventId = $eventId;
        $this->sendingDate = $sendingDate;
    }

    private function createSmsLogs ($responses)
    {
        $toInsert = [];
        $today = Carbon::now('Asia/Manila');

        if(count($responses) > 0)
        {
            foreach($responses as $response)
            {
                $toInsert[] = [
                    'maker_id' => $this->userId,
                    'message_id' => $response['message_id'],
                    'user_id' => $response['user_id'],
                    'user' => $response['user'],
                    'account_id' => $response['account_id'],
                    'account' => $response['account'],
                    'recipient' => $response['recipient'],
                    'message' => $response['message'],
                    'sender_name' => $response['sender_name'],
                    'network' => $response['network'],
                    'type' => $response['type'],
                    'source' => $response['source'],
                    'sending_type' => $this->sendingType,
                    'event_id' => $this->eventId,
                    'created_at' => $today,
                    'updated_at' => $today,
                ];
            }

            return SmsLogs::insert($toInsert);
        }

        return null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $semphoreClient = new SemaphoreClient( env('SEMAPHORE_API_KEY'), $this->options);

        if(is_array($this->recepient))
        {
            $newRecepient = collect($this->recepient)->implode('recepient', ',');
    
            $response = $semphoreClient->send($newRecepient, $this->message);

        } else 
        {
            $response = $semphoreClient->send($this->recepient, $this->message);
        }

        $this->createSmsLogs(
            json_decode($response->getContents(), true)
        );
        
        SmsJobHistory::where('job_id', $this->job->getJobId())->update(['status' => 'Success']);
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
       info('Error Processing Sms Job: '. $exception->getMessage());

       SmsJobHistory::where('job_id', $this->job->getJobId())->update(['status' => 'Failed']);
    }
}
