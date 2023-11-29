<?php

namespace App\Http\Controllers\OneBITS\Payment\PaymentResponse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PayMaya\PayMayaSDK;
use App\Models\OneBITS\Ticket;
use Carbon\Carbon;
use App\Models\Transportation\Trip;
use Illuminate\Support\Facades\Mail;
use App\Mail\OneBITS\FailedBooking;
use App\Models\Transportation\SeatSegment;
use App\Models\Booking\Guest;
use Illuminate\Support\Facades\DB;

class PaymayaCancel extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $response = $request->all();
        Log::channel("onebitspayment")->info($response);
        PayMayaSDK::getInstance()->initCheckout(env('MAYA_PUBLIC_API_KEY'), env('MAYA_SECRET_API_KEY'), env('MAYA_API_ENDPOINT_ENV'));

        $tickets = Ticket::where('group_reference_number', $response['reference_number'])
            ->with('passenger')
            ->with('schedule.route.origin')
            ->with('schedule.route.destination')
            ->with('schedule.transportation')
            ->with('trip')
            ->get();

        DB::transaction(function () use ($tickets, $response) {
            Ticket::where('group_reference_number', $response['reference_number'])->update([
                'paid_at' => Carbon::now(),
                'payment_status' => 'voided',
                'payment_provider_reference_number' => null,
                'payment_provider' => 'maya',
                'payment_channel' => 'maya',
                'status' => 'voided',
            ]);
            $pluck_ticket_ref_no = collect($tickets)->pluck('reference_number')->all();
            $trip = Trip::whereIn('ticket_reference_number', $pluck_ticket_ref_no)->where("status", "pending")->get();
            $pluck_seat_segment_id = collect($trip)->pluck('seat_segment_id')->all();
            Trip::whereIn('ticket_reference_number', $pluck_ticket_ref_no)->update([
                'status' => 'cancelled',
                'checked_in_at' => Carbon::now(),
            ]);
            SeatSegment::whereIn("id", $pluck_seat_segment_id)->decrement('used');
        });

        $passenger_email = $tickets[0]['email'];
        Mail::to($passenger_email)
            // ->cc($additional_emails)
            ->send(new FailedBooking());
        return redirect(env('ONEBITS_URL') . '/book/payment/response?err=' . "Your Booking has been cancelled")->with($response);
    }
}
