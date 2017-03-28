<?php

namespace Eyewitness\Eye\Witness;

use Exception;
use Config;
use Mail;
use Log;

class Email
{
    /**
     * Try to send an email to Eyewitness to confirm emails are ok.
     *
     * @return void
     */
    public function send()
    {
        try {
            if (Config::get('eye::send_queued_emails')) {
                $this->sendQueuedMail();
            } else {
                $this->sendImmediateMail();
            }
        } catch (Exception $e) {
            Log::error('Unable to send Eyewitness.io email: '.$e->getMessage());
        }
    }

    /**
     * Send an queued email to Eyewitness to confirm emails are ok.
     *
     * @return void
     */
    protected function sendQueuedMail()
    {
        Mail::queue('eye::email', ['timestamp' => time()], function ($message) {
            $message->to(Config::get('eye::app_token').'@eyew.io', 'Eyewitness.io');
            $message->subject('Ping Eyewitness');
        });
    }

    /**
     * Send an immediate email to Eyewitness to confirm emails are ok.
     *
     * @return void
     */
    protected function sendImmediateMail()
    {
        Mail::send('eye::email', ['timestamp' => time()], function ($message) {
            $message->to(Config::get('eye::app_token').'@eyew.io', 'Eyewitness.io');
            $message->subject('Ping Eyewitness');
        });
    }
}
