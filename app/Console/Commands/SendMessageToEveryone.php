<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Console\Command;

class SendMessageToEveryone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordlebot:send-message-to-everyone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blast a message to everyone.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $twilioService = new TwilioService();

        $allUsers = User::all();
        $message = 'Happy thanksgiving! I was under construction all this morning but hopefully should be back now :) Please resend me your score if you sent it in earlier!';
        foreach ($allUsers as $user) {
            $twilioService->sendMessage($user->phone_number, $message);
        }
    }
}
