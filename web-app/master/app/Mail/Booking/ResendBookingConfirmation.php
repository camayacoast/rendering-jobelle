<?php

namespace App\Mail\Booking;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking\Guest;

use App\Models\Booking\Booking;
use PDF;
// use Barryvdh\DomPDF\Facade as PDF;
// use Barryvdh\DomPDF\PDF;

class ResendBookingConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $booking;
    protected $camaya_transportations;
    protected $reference_number;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, $camaya_transportations,$reference_number)
    {
        //
        $this->booking = $booking;
        $this->camaya_transportations = $camaya_transportations;
        $this->reference_number = $reference_number;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $uniqueMeals = [];
        if (!empty($this->reference_number) && !is_null($this->reference_number) && !$this->reference_number = "") {

            $guest_meals = Guest::select('meals.type', 'meals.name')
                ->where('guests.booking_reference_number', $this->reference_number)
                ->leftJoin('guest_meals', "guest_meals.guest_id", "guests.id")
                ->leftJoin('meals', 'meals.id', 'guest_meals.meal_id')->get();


            // $uniqueMeals = $guest_meals->unique(function ($item) {
            //     return $item['meal_name'] . $item['meal_type'];
            // });

            echo $this->reference_number;
        }

        $booking_confirmation_pdf = \PDF::loadView('pdf.booking.booking_pending', ['booking' => $this->booking, 'camaya_transportations' => $this->camaya_transportations,'guest_meals' => $uniqueMeals]);

        return $this
                ->subject('[RESEND] Booking | Booking ref #:'. $this->booking['reference_number'])
                ->from('reservations@camayacoast.com', 'Camaya Reservations')
                ->cc(env('APP_ENV') == 'production' ? ['itinfrateamcamaya@gmail.com','reservations@camayacoast.com'] : 'adrian.ardais@camayacoast.com')
                ->with([
                        'booking' => $this->booking
                ])
                ->attachData($booking_confirmation_pdf->output(), 'PENDING BOOKING '.$this->booking['reference_number'].'.pdf', [
                        'mime' => 'application/pdf',
                ])
                // ->attach(public_path() . '/attachments/house_rules_and_hotel_resort_guidelines_and_policies_sept7_compressed.pdf', [
                //     'as' => 'RESORT_GUIDELINES.pdf',
                //     'mime' => 'application/pdf',
                // ])
                // ->attach(public_path() . '/attachments/camaya_vaccination_update_0906_compressed.pdf', [
                //     'as' => 'CAMAYA_VACCINATION_UPDATE_0906.pdf',
                //     'mime' => 'application/pdf',
                // ])
                ->markdown('emails.booking.new_booking');

    }
}

