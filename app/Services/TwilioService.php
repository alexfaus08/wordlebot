<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Twilio\Rest\Client;

class TwilioService
{
    public function sendMessage(string $to, string $body): void
    {
        $twilioActive = getenv('TWILIO_ACTIVE');
        $twilioActive = $twilioActive === 'true';
        if ($twilioActive || App::environment('production')) {
            $accountSid = getenv('TWILIO_SID');
            $authToken = getenv('TWILIO_AUTH_TOKEN');
            $phoneNum = getenv('TWILIO_NUMBER');

            $client = new Client($accountSid, $authToken);
            $client->messages->create($to, ['from' => $phoneNum, 'body' => $body]);
        }
    }

    public function sendScoreBoardMessage(User $user): string
    {
        $scoreCalculator = new FamilyScoreBoardGeneratorService($user);
        $message = $scoreCalculator->getDailyScoreboardMessagesForAllFamilies();

        $this->sendMessage($user->phone_number, $message);

        return $message;
    }

    public function sendMessageToEveryone(string $message): string
    {
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            $this->sendMessage($user->phone_number, $message);
        }

        return $message;
    }
}
