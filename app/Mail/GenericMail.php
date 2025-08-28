<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public $view;
    public $data;
    public $subjectLine;

    public function __construct($view, $data = [], $subjectLine = 'NAC Tax Center')
    {
        $this->view = $view;
        $this->data = $data;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view($this->view, $this->data);
    }
}
