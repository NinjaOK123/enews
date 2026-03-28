<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = !empty($this->data['subject']) ? $this->data['subject'] : 'Liên hệ từ độc giả';
        return $this->subject('[e-News] ' . $subject)
                    ->replyTo($this->data['email'], $this->data['name'])
                    ->view('emails.contact');
    }
}
