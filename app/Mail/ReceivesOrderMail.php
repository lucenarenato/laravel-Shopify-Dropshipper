<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceivesOrderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $params;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // logger("mail--------------------.");
       // logger("user receives an order the email! ");
        $subject = "You Have a New Order!";

        return $this->view('emails.receives-order')->with([
            'params' => $this->params
        ])->subject($subject);

        // logger("--------------------mail.");
    }
}
