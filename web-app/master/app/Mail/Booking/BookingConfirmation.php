<?php

namespace App\Mail\Booking;

use App\Models\Booking\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


use App\Models\Booking\Booking;
use PDF;


class BookingConfirmation extends Mailable implements ShouldQueue
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
    public function __construct(Booking $booking, $camaya_transportations, $reference_number)
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
        if (!empty($this->reference_number) && !is_null($this->booking['reference_number']) && !$this->reference_number = "") {

            $guest_meals = Guest::select('meals.type', 'meals.name')
                ->where('guests.booking_reference_number', $this->booking['reference_number'])
                ->leftJoin('guest_meals', "guest_meals.guest_id", "guests.id")
                ->leftJoin('meals', 'meals.id', 'guest_meals.meal_id')
                ->select(
                    "meals.name as meal_name",
                    "meals.type as meal_type",
                )->get();


            $uniqueMeals = $guest_meals->unique(function ($item) {
                return $item['meal_name'] . $item['meal_type'];
            });
            // echo "fuck";

        }

        // echo $this->booking['reference_number'];

        $booking_confirmation_pdf = PDF::loadView('pdf.booking.booking_confirmation', ['booking' => $this->booking, 'camaya_transportations' => $this->camaya_transportations, 'guest_meals' => $uniqueMeals]);

        $this->subject('Booking Confirmation | Booking ref #:' . $this->booking['reference_number'])
            ->from('reservations@camayacoast.com', 'Camaya Reservations')
            ->cc(env('APP_ENV') == 'production' ? ['itinfrateamcamaya@gmail.com','reservations@camayacoast.com'] : 'adrian.ardais@camayacoast.com')
            ->with([
                'booking' => $this->booking
            ])
            ->attachData($booking_confirmation_pdf->output(), 'BOOKING CONFIRMATION ' . $this->booking['reference_number'] . '.pdf', [
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
            // ->attach(public_path('images/splashing_getaway.jpg'), [
            //     'as' => 'Splashing_Getaway.jpg',
            //     'mime' => 'image/jpeg',
            // ])
            // ->attach(public_path('images/bus.png'), [
            //     'as' => 'Iterenary.png',
            //     'mime' => 'image/png',
            // ])
            ->markdown('emails.booking.booking_confirmation');

        foreach ($this->booking['guests'] as $guest) {
            $uniqueTranspo = [];
            if (count($this->camaya_transportations) > 0) {
                $uniqueTranspo = $this->camaya_transportations->unique(function ($item) {
                    return $item['transportation']['type'];
                });
            }
            $boarding_pass = PDF::loadView('pdf.booking.boarding_pass', ['guest' => $guest, 'camaya_transportations' => $uniqueTranspo]);

            $pass_type = "GATE PASS";

            if (count($this->camaya_transportations) > 0) {
                $pass_type = "BOARDING PASS";
            }

            $this->attachData($boarding_pass->output(), $guest['first_name'] . ' ' . $guest['last_name'] . ' ' . $guest['reference_number'] . ' ' . $pass_type . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $this;
    }
}
