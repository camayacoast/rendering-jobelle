<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Booking\CreateSmsEvent;
use Semaphore\SemaphoreClient;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\Booking\SmsLogs;
use App\Models\Booking\SmsSubject;
use App\Models\Booking\SmsEvent;
use App\Models\Booking\SmsJobHistory;

use Carbon\Carbon;

use App\Imports\ImportSmsRecepients;
use App\Exports\SmsRecepientTemplate;
use App\Exports\SmsEventExport;

use App\Jobs\ProcessScheduledSms;

use Illuminate\Contracts\Bus\Dispatcher;

use DB;

class SmsNotification extends Controller
{
    private $semaphoreClient;
    private $options = ['sendername' => 'camayacoast', 'apiBase' => 'https://api.semaphore.co/api/v4/messages'];

    public function __construct()
    {   
        $this->semaphoreClient = new SemaphoreClient( env('SEMAPHORE_API_KEY'), $this->options);
    }

    public function getRecepients (Request $request)
    {
        return [];
    }

    public function sendBulkMessage (Request $request)
    {
        try {

            $sendingType = $request['sending_type'];
            $message = $request['message'];
            $recepients = $request['recepients'];
            $scheduleDate = $request['schedule_date'];
            $eventId = $request['event_id'];
            $userId = Auth::id();
            $today = Carbon::now('Asia/Manila');

            $eventData = SmsEvent::find($eventId);

            $job = new ProcessScheduledSms($userId, $recepients, $sendingType, $message, $eventId, $today);

            if($sendingType === 'immediate')
            {
                $jobId = app(Dispatcher::class)->dispatch($job);

                $sendingDate = ['sending_date' => $today];
            }
            else if($sendingType === 'schedule' && isset($scheduleDate))
            {
                $parseDate = Carbon::parse($scheduleDate);

                $jobId = app(Dispatcher::class)->dispatch($job->delay($parseDate));

                $sendingDate = ['sending_date' => $parseDate];
            }

            SmsJobHistory::create(array_merge(
                [
                    'maker_id' => $userId,
                    'event_id' => $eventId,
                    'job_id' => $jobId,
                    'status' => 'Pending',
                    'sending_type' => $sendingType,
                    'event_name' => $eventData->event_name,
                    'recepients' => json_encode($recepients),
                    'event_description' => $eventData->event_description,
                    'message' => $eventData->message,
                ],
                $sendingDate
            ));
    
            return response(['message' => 'success'], 200);

        } catch(\Exception $e) {

            $error = explode(':', $e->getMessage());

            return response(['message' => 'Error processing this request!','errors' => $e->getMessage()], 500);
        }
    }

    public function sendImportBulkMessage (Request $request)
    {
        try {
            $sendingType = $request['sending_type'];
            $datas = $request['data'];
            $scheduleDate = $request['schedule_date'];
            $userId = Auth::id();
            $today = Carbon::now('Asia/Manila');

            if($sendingType === 'immediate')
            {
                if(count($datas) > 0)
                {
                    foreach($datas as $data)
                    {
                        ProcessScheduledSms::dispatch($userId, trim($data['recepient']), $sendingType, $data['message'], 0, $today);
                    }

                }  

            } elseif($sendingType === 'schedule' && isset($scheduleDate))
            {
                if(count($datas) > 0)
                {
                    foreach($datas as $data)
                    {
                        $parseDate = Carbon::parse($scheduleDate);
                
                        ProcessScheduledSms::dispatch($userId, trim($data['recepient']), $sendingType, $data['message'], 0, $scheduleDate)->delay($parseDate);
                    }

                }
            }
    
            return response(['message' => 'success'], 200);

        } catch(\Exception $e) {

            $error = explode(':', $e->getMessage());

            return response(['message' => 'Error processing this request!', 'errors' => $e->getMessage()], 500);
        }
    }

    public function getMessages ()
    {
        $data = json_decode($this->semaphoreClient->messages([
            'limit' => 1000,
            'startDate' => Carbon::today('Asia/Manila')->format('Y-m-d'),
            'endDate' => Carbon::today('Asia/Manila')->format('Y') . '-12-31'
        ])->getContents(), true);

        $collection = collect($data)->map(function($item) 
        {
            return array_merge($item, 
                ['created_at' => Carbon::parse($item['created_at'])->format('Y-m-d')],
                ['month_created' => Carbon::parse($item['created_at'])->format('m')],
                ['year_created' => Carbon::parse($item['created_at'])->format('Y')]
            );
        });

        // $countSentToday = $collection->where('created_at', '=', Carbon::now('Asia/Manila')->format('Y-m-d'))->where('status', '=', 'Sent')->count();
        // $countMonthlySent = $collection->where('month_created', '=', Carbon::now('Asia/Manila')->format('m'))->where('status', '=', 'Sent')->count();
        // $countYearlySent = $collection->where('year_created', '=', Carbon::now('Asia/Manila')->format('Y'))->where('status', '=', 'Sent')->count();
        
        return response([
            'data' => $collection->sortDesc()->values()->all(),
            'countSentToday' => 0,
            'countMonthlySent' => 0,
            'countYearlySent' => 0
        ], 200);
    }

    public function getAccounts ()
    {
        return $this->semaphoreClient->account();
    }

    public function importRecepients (Request $request)
    {
        $importInstance = new ImportSmsRecepients;

        Excel::import($importInstance, request()->file('file'));

        return response(['data' => $importInstance->data], 200);
    }

    public function exportRecepientTemplate (Request $request)
    {
        return Excel::download(new SmsRecepientTemplate, 'report.xlsx'); 
    }

    public function exportEventJob (Request $request)
    {
        return Excel::download(new SmsEventExport($request->id), 'report.xlsx'); 
    }

    public function getSmslogs (Request $request)
    {

        $dateToday = Carbon::now('Asia/Manila');

        $data = SmsLogs::leftJoin('users', 'tbl_sms_logs.maker_id', '=', 'users.id')
            ->select(
                'users.first_name',
                'users.last_name',
                'tbl_sms_logs.recipient',
                'tbl_sms_logs.message',
                'tbl_sms_logs.sender_name',
                'tbl_sms_logs.network',
                'tbl_sms_logs.sending_type',
                DB::raw('DATE_FORMAT(tbl_sms_logs.created_at, "%Y-%m-%d") as created_date'),
                DB::raw('DATE_FORMAT(tbl_sms_logs.created_at, "%m") as month_created'),
                DB::raw('DATE_FORMAT(tbl_sms_logs.created_at, "%Y") as year_created')
            )
        ->orderByDesc('tbl_sms_logs.created_at')
        ->get();

        $countSentToday = $data->where('created_date', '=', $dateToday->format('Y-m-d'))->count();
        $countMonthlySent = $data->where('month_created', '=', $dateToday->format('m'))->count();
        $countYearlySent = $data->where('year_created', '=', $dateToday->format('Y'))->count();
        
        return response([
            'data' => $data,
            'countSentToday' => $countSentToday,
            'countMonthlySent' => $countMonthlySent,
            'countYearlySent' => $countYearlySent
        ], 200);
    }

    public function createSubject (Request $request)
    {
        try {
            $subject = $request['subjectInput'];

            $newSubject = SmsSubject::create(['subject' => $subject]);

            return response(['message' => 'success', 'data' => $newSubject], 200);

        } catch (\Exception $e) {
            return response(['message' => 'Error processing this request!', 'errors' => $e->getMessage()], 500);
        }
    }

    public function getSubjects ()
    {
        return SmsSubject::orderByDesc('created_at')->get();
    }

    public function createEvent (CreateSmsEvent $request)
    {
        try {

            $newEvent = SmsEvent::create([
                'event_name' => $request['event_name'],
                'recepients' => json_encode($request['recepients']),
                'event_description' => $request['event_description'],
                'message' => $request['message']
            ]);

            return response(['message' => 'success', 'data' => $newEvent], 200);

        } catch (\Exception $e) {
            return response(['message' => 'Error processing this request!','errors' => $e->getMessage()], 500);
        }
    }

    public function updateEvent (Request $request)
    {
        try {

            $updateEvent = SmsEvent::where('id', $request['id'])->update(
                [
                    'event_name' => $request['event_name'],
                    'recepients' => json_encode($request['recepients']),
                    'event_description' => $request['event_description'],
                    'message' => $request['message']
                ]
                );

            return response(['message' => 'success', 'data' => $updateEvent], 200);

        } catch (\Exception $e) {
            return response(['message' => 'Error processing this request!','errors' => $e->getMessage()], 500);
        }
    }

    public function removeEvent (Request $request)
    {
        try {

            $removeEvent = SmsEvent::find($request['id'])->delete();

            return response(['message' => 'success', 'data' => $removeEvent], 200);

        } catch (\Exception $e) {
            return response(['message' => 'Error processing this request!','errors' => $e->getMessage()], 500);
        }
    }

    public function getEvents ()
    {
        return SmsEvent::with('eventHistory')->orderByDesc('created_at')->get();
    }

    public function getEventsJobHistory ()
    {
        return SmsJobHistory::leftJoin('tbl_sms_events', 'tbl_sms_job_history.event_id', '=', 'tbl_sms_events.id')
                    ->select(
                        'tbl_sms_job_history.id',
                        'tbl_sms_job_history.job_id',
                        'tbl_sms_events.event_name',
                        'tbl_sms_events.event_description',
                        'tbl_sms_job_history.status',
                        'tbl_sms_job_history.sending_type',
                        'tbl_sms_job_history.sending_date',
                        'tbl_sms_job_history.sending_type',
                        'tbl_sms_job_history.created_at',
                    )
                ->orderByDesc('tbl_sms_job_history.id')
                ->get();
    }

    public function removeJobs (Request $request)
    {
        try {

            $id = $request['id'];

            $removeJobs = DB::table('jobs')->delete($id);

            SmsJobHistory::where('job_id', $id)->update(['status' => 'Cancelled']);

            return response(['message' => 'success', 'data' => $removeJobs], 200);

        } catch (\Exception $e) {
            return response(['message' => 'error', 'Error processing this request!','errors' => $e->getMessage()], 500);
        }
    }
}
