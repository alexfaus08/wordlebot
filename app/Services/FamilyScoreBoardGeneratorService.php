<?php

namespace App\Services;

use App\Models\Family;
use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class FamilyScoreBoardGeneratorService
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getDailyScoreboardMessagesForAllFamilies(): string
    {
        $families = $this->user->families;
        $messages = [];
        foreach ($families as $family) {
            $messages[] = $this->getDailyScoreboard($family);
        }

        return implode(PHP_EOL.PHP_EOL.'---'.PHP_EOL.PHP_EOL, $messages);
    }

    public function getWeeklyScoreboardMessagesForAllFamilies(): string
    {
        $families = $this->user->families;
        $messages = [];

        // this command is run on a Monday
        // we want scores from last Monday to last Sunday
        $now = Carbon::now()->startOfDay();
        // This should be a Sunday
        $endOfWeek = $now->copy()->subDays()->endOfDay();
        // This should be a Monday
        $startOfWeek = $now->copy()->subDays(7);

        foreach ($families as $family) {
            $messages[] = $this->getWeeklyScoreboard($family, $startOfWeek, $endOfWeek);
        }

        return implode(PHP_EOL.'---'.PHP_EOL, $messages);
    }

    public function getDailyScoreboard(Family $family): string
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
            if ($score->value === 7) {
                $score->value = 'X';
            }
            $previousScore = $score->value;

            $message .= $this->addOrdinalNumberSuffix($place).' - '.$score->user->name.' '.$score->value.'/6'.PHP_EOL;
        }
        if ($userNamesWhoDidNotPlay->count() > 0) {
            $message .= PHP_EOL.'Did not play yet: '.$userNamesWhoDidNotPlay->join(', ', ' and ');
        }

        $message .= PHP_EOL.PHP_EOL.'View family score blocks here: https://familyscores.com/family/'.$family->id;

        return $message;
    }

    public function getWeeklyScoreboard(Family $family, Carbon $start, Carbon $end): string
    {
        $message = $family->name.' Last Week\'s Scoreboard:'.PHP_EOL;
        $familyUsers = $family->users;
        $totalDays = $end->diffInDays($start) + 1;
        $allWeeklyScores = [];

        foreach ($familyUsers as $user) {
            $scores = Score::whereBetween('created_at', [$start, $end])
                ->where('user_id', $user->id)
                ->select('value')
                ->get();
            $missingDays = $totalDays - $scores->count();
            $penalties = $missingDays * 7;
            $weeklyScore = $scores->pluck('value')->sum() + $penalties;
            $allWeeklyScores[] = ['name' => $user->name, 'value' => $weeklyScore];
        }
        $sortedWeeklyScores = array_values(Arr::sort($allWeeklyScores, function ($score) {
            return $score['value'];
        }));

        $place = 1;
        $previousScore = $sortedWeeklyScores[0]['value'];

        foreach ($sortedWeeklyScores as $weeklyScore) {
            if ($weeklyScore['value'] !== $previousScore) {
                $place += 1;
            }
            $previousScore = $weeklyScore['value'];

            $message .= $this->addOrdinalNumberSuffix($place).' '.$weeklyScore['name'].': '.$weeklyScore['value'].PHP_EOL;
        }

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
