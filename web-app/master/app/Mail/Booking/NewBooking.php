<?php

namespace App\Mail\Booking;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Booking\Booking;
use App\Models\Booking\GuestMeals;
use PDF;
// use Barryvdh\DomPDF\Facade as PDF;

class NewBooking extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $booking;
    protected $camaya_transportations;
    protected $guest_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, $camaya_transportations, $guest_id)
    {
        //
        $this->booking = $booking;
        $this->camaya_transportations = $camaya_transportations;
        $this->guest_id = $guest_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $uniqueMeals = [];
        if (!empty($this->guest_id) && !is_null($this->guest_id)) {

            $guest_meals = GuestMeals::whereIn("guest_id", $this->guest_id)->leftJoin("meals", "meals.id", "guest_meals.meal_id")->select(
                    "meals.name as meal_name",
                    "meals.type as meal_type",
                )->get();

            $uniqueMeals = $guest_meals->unique(function ($item) {
                return $item['meal_name'] . $item['type'];
            });
        }

        $booking_confirmation_pdf = PDF::loadView('pdf.booking.booking_pending', ['booking' => $this->booking, 'camaya_transportations' => $this->camaya_transportations, 'guest_meals' => $uniqueMeals]);

        return $this
            ->subject('New Booking | Booking ref #:' . $this->booking['reference_number'])
            ->from('reservations@camayacoast.com', 'Camaya Reservations')
            ->cc(env('APP_ENV') == 'production' ? ['itinfrateamcamaya@gmail.com','reservations@camayacoast.com'] : 'adrian.ardais@camayacoast.com')
            ->with([
                'booking' => $this->booking,
            ])
            ->attachData($booking_confirmation_pdf->output(), 'PENDING BOOKING ' . $this->booking['reference_number'] . '.pdf', [
                'mime' => 'application/pdf',
            ])
            // ->attach(public_path() . '/attachments/house_rules_and_hotel_resort_guidelines_and_policies_sept7_compressed.pdf', [
            //     'as' => 'RESORT_GUIDELINES.pdf',
            //     'mime' => 'application/pdf',
            // ])
            ->markdown('emails.booking.new_booking');
    }
}
