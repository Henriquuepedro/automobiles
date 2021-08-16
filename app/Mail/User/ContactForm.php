<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactForm extends Mailable
{
    use Queueable, SerializesModels;

    private $form;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $form)
    {
        $this->form = $form;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->subject)
            ->to($this->form->mail_to, config('mail.from.name'));

        return $this->markdown('user.mail.contact', [
            'form' => $this->form
        ]);
    }
}
