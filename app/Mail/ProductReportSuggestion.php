<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductReportSuggestion extends Mailable
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
        //logger("report-suggestion-mail build called! ");

        $subject = '';

        if($this->params['report_type']=="suggestion"){
            $subject = "Suggestion";
            }
        else{
            $subject = "Report an Error";
        }

        return $this->view('emails.report-suggestion')->with([
            'params' => $this->params
        ])->subject($subject);

       // logger("--------------------mail.");
    }
}
