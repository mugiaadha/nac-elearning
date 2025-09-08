<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $messageText;
    public $subjectLine;

    public function __construct($name, $email, $messageText, $subjectLine = 'Pesan Baru dari Pengunjung NAC')
    {
        $this->name = $name;
        $this->email = $email;
        $this->messageText = $messageText;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.contact');
    }
}
