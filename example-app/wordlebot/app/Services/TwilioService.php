<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    public function sendMessage(string $to, string $body): void
    {
        $twilioActive = getenv('TWILIO_ACTIVE');
        $isProduction = getenv('APP_ENV') === 'local';
        $twilioActive = $twilioActive === 'true';
        $isProduction = $isProduction === 'true';
        if ($twilioActive || $isProduction) {
            $accountSid = getenv('TWILIO_SID');
            $authToken = getenv('TWILIO_AUTH_TOKEN');
            $phoneNum = getenv('TWILIO_NUMBER');

            $client = new Client($accountSid, $authToken);
            $client->messages->create($to, ['from' => $phoneNum, 'body' => $body]);
        }
    }
}
