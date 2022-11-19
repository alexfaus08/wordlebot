<?php

namespace App\Services;

use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ScoreBoardGeneratorService
{
    public function getDailyScoreboardMessage(): string
    {
        $scores = Score::whereDate('created_at', Carbon::today())
            ->orderBy('value')
            ->get();

        return self::sortedScoresToScoreboard($scores, 'Today\'s Scoreboard:');
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

    public function getWeeklyScoreboardMessage()
    {
        // this command is run on a Monday
        // we want scores from last Monday to last Sunday
        $now = Carbon::now()->startOfDay();
        // This should be a Sunday
        $endOfWeek = $now->copy()->subDays()->endOfDay();
        // This should be a Monday
        $startOfWeek = $now->copy()->subDays(7);
        $scores = Score::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get();
        $allUsers = User::all();
        $totalDays = 7;
        $weeklyScores = [];
        foreach ($allUsers as $user) {
            $userScores = $scores->filter(function ($score) use ($user) {
                return $score->user_id === $user->id;
            });
            $penalties = $totalDays - count($userScores);
            $penalties = $penalties * 7;
            $totalScore = $userScores->sum('value') + $penalties;
            $weeklyScores[] = ['user' => $user, 'value' => $totalScore];
        }
        $sorted = array_values(Arr::sort($weeklyScores, function ($score) {
            return $score['value'];
        }));
        $weeklyScoresCollection = collect($sorted);
        return self::sortedScoresToScoreboard($weeklyScoresCollection, 'Last Week\'s Scoreboard:', false);
    }

    // $sortedScores are scores sorted in ascending order
    private function sortedScoresToScoreboard(Collection $sortedScores, string $title, $isDailyScoreboard = true): string
    {
        $rankings = [];
        $previousScore = null;
        $currentPlace = 0;
        if (count($sortedScores) === 0 && $isDailyScoreboard) {
            return 'No one has played today :(';
        }
        for ($i = 0; $i < count($sortedScores); $i++) {
            if ($sortedScores[$i]['value'] !== $previousScore) {
                $currentPlace += 1;
            }
            $previousScore = $sortedScores[$i]['value'];
            $scoreValue = $sortedScores[$i]['value'];
            if ($isDailyScoreboard) {
                $scoreValue = $scoreValue === 7 ? 'X' : $sortedScores[$i]['value'];
                $scoreValue .= '/6';
            }
            $rankings[] = ['place' => $this->addOrdinalNumberSuffix($currentPlace), 'name' => $sortedScores[$i]['user']['name'], 'score' => $scoreValue];
        }

        $message = $title;

        foreach ($rankings as $ranking) {
            $message .= PHP_EOL.$ranking['place'].' - '.$ranking['name'].' '.$ranking['score'];
        }

        if ($isDailyScoreboard) {
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
        }

        return $message;
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
