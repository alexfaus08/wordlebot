<?php

namespace App\Console\Commands;

use App\Models\Score;
use App\Models\User;
use App\Services\TwilioService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminderToPlay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordlebot:send-reminder-to-play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a reminder to anyone who has not yet played Wordle today to play.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $twilioService = new TwilioService();
        $usersPlayedToday = Score::whereDate('created_at', Carbon::today())
            ->select('user_id')
            ->get();
        $usersNotPlayedToday = User::whereNotIn('id', $usersPlayedToday)
            ->get();
        $message = 'Don\'t forget to play Wordle today!'.'https://www.nytimes.com/games/wordle/index.html';
        foreach ($usersNotPlayedToday as $user) {
            $twilioService->sendMessage($user->phone_number, $message);
        }

        return 0;
    }
}
