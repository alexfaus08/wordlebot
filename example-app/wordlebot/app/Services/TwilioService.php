<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    public function sendMessage(string $body): void {
        $account_sid = getenv('TWILIO_SID');
        $auth_token = getenv('TWILIO_AUTH_TOKEN');
        $phone_num = getenv('TWILIO_NUMBER');

        $client = new Client($account_sid, $auth_token);
        $client->messages->create('*6183403117', ['from' => $phone_num, 'body' => $body]);
    }
}
