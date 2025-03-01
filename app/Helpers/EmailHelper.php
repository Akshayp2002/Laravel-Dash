<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    /**
     * Send a generic email
     *
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $data
     * @return bool
     */
    public static function sendEmail($to, $subject, $view, $data = [])
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });
            return true; // Email sent successfully
        } catch (\Exception $e) {
            return false; // Failed to send email
        }
    }

    public static function sendPlainEmail($to, $subject, $message)
    {
        try {
            Mail::raw($message, function ($mail) use ($to, $subject) {
                $mail->to($to)->subject($subject);
            });
            return true;  // Email sent successfully
        } catch (\Exception $e) {
            return false; // Failed to send email
        }
    }

    
}
