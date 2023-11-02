<?php

namespace App\Services;

use App\Models\Family;
use App\Models\User;
use Carbon\Carbon;

class FamilyScoreBoardGeneratorService
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function sendDailyScoreboardMessage()
    {
        $families = $this->user->families;
        $messages = [];
        foreach ($families as $family) {
            $messages[] = $this->getDailyScoreboard($family);
        }
        dd($messages);
    }

    public function getDailyScoreboard(Family $family)
    {
        $message = $family->name.' Scoreboard:'.PHP_EOL;
        $scores = $family->getSortedScoresForDate(Carbon::today());
        if ($scores->count() === 0) {
            $message .= 'No one has played today :(';
            return $message;
        }

        $usersWhoPlayed = $scores->pluck('user')->pluck('id');
        $userNamesWhoDidNotPlay = $family->users()->whereNotIn('user_id', $usersWhoPlayed)->get()->pluck('name');

        $place = 1;
        $previousScore = $scores[0]->value;
        foreach ($scores as $score) {
            if ($score->value !== $previousScore) {
                $place += 1;
            }
            $message .= $this->addOrdinalNumberSuffix($place).' '.$score->user->name.': '.$score->value.PHP_EOL;
        }
        $message .= 'Did not play yet: '.$userNamesWhoDidNotPlay->join(', ', ' and ');

        return $message;
    }

    private function addOrdinalNumberSuffix($place)
    {
        if (! in_array(($place % 100), [11, 12, 13])) {
            switch ($place % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:  return $place.'st';
                case 2:  return $place.'nd';
                case 3:  return $place.'rd';
            }
        }

        return $place.'th';
    }
}
