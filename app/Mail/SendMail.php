<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public  $title;
    public $userinfo;
    public $activationLink;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $userinfo, $activationLink)
    {
        $this->title = $title;
        $this->userinfo = $userinfo;
        $this->activationLink = $activationLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)->view('userMail');
    }
}