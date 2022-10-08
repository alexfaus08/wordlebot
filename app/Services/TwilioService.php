<?php

namespace App\Services;

use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;
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

    public function sendScoreBoardMessage($to): string
    {
        $scores = Score::whereDate('created_at', Carbon::today())
            ->orderBy('value')
            ->get();
        $rankings = [];
        $previousScore = null;
        $currentPlace = 0;
        if (count($scores) === 0) {
            $this->sendMessage($to, 'No one has played today :(');
            return 'No one has played today :(';
        }
        for ($i = 0; $i < count($scores); $i++) {
            if ($scores[$i]->value !== $previousScore) {
                $currentPlace += 1;
            }
            $previousScore = $scores[$i]->value;
            $scoreValue = $scores[$i]->value === 0 ? 'X' : $scores[$i]->value;
            $rankings[] = ['place' => $this->addOrdinalNumberSuffix($currentPlace), 'name' => $scores[$i]->user->name, 'score' => $scoreValue.'/6'];
        }
        $message = 'Today\'s Leaderboard:';
        foreach ($rankings as $ranking) {
            $message = $message.PHP_EOL.$ranking['place'].' - '.$ranking['name'].' '.$ranking['score'];
        }

        $usersWhoHaveNotPlayed = $this->getUsersWhoHaveNotPlayerToday();
        if (count($usersWhoHaveNotPlayed) > 0) {
            $notPlayedMessage = '';
            if (count($usersWhoHaveNotPlayed) === 1) {
                $notPlayedMessage = $usersWhoHaveNotPlayed[0]->name;
            } else {
                for ($u = 0; $u < count($usersWhoHaveNotPlayed); $u++) {
                    if ($u == count($usersWhoHaveNotPlayed) - 1) {
                        $notPlayedMessage .= 'and ';
                        $notPlayedMessage .= $usersWhoHaveNotPlayed[$u]->name;
                    } else {
                        $notPlayedMessage .= $usersWhoHaveNotPlayed[$u]->name;
                        $notPlayedMessage .= ', ';
                    }
                }
            }
            $message .= PHP_EOL.PHP_EOL.'Not played yet: '.$notPlayedMessage;
        }

        $this->sendMessage($to, $message);

        return $message;
    }

    public function getUsersWhoHaveNotPlayerToday()
    {
        $usersPlayedToday = Score::whereDate('created_at', Carbon::today())
            ->select('user_id')
            ->get();
        $usersNotPlayedToday = User::whereNotIn('id', $usersPlayedToday)
            ->get();

        return $usersNotPlayedToday;
    }

    private function addOrdinalNumberSuffix($num)
    {
        if (! in_array(($num % 100), [11, 12, 13])) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:  return $num.'st';
                case 2:  return $num.'nd';
                case 3:  return $num.'rd';
            }
        }

        return $num.'th';
    }
}
