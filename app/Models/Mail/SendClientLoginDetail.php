<?php

namespace App\Models\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Utility;

class SendClientLoginDetail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Client $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {  
        $setting = Utility::getAdminPaymentSettings();
        return $this->markdown('email.login.client_detail')->subject('Login details - '.$setting['app_name'] ? $setting['app_name'] : 'Taskly');
    }
}
