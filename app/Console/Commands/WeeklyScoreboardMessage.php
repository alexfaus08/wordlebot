<?php

namespace App\Console\Commands;

use App\Services\ScoreBoardGeneratorService;
use App\Services\TwilioService;
use Illuminate\Console\Command;

class WeeklyScoreboardMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordlebot:send-weekly-scoreboard-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'On every monday, send the scoreboard for the last week of Wordle scores.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $scoreCalculationService = new ScoreBoardGeneratorService();
        $weeklyScoreboardMessage = $scoreCalculationService->getWeeklyScoreboardMessage();
        $twilioService = new TwilioService();
        $twilioService->sendMessageToEveryone($weeklyScoreboardMessage);

        return 0;
    }
}
