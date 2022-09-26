<?php

namespace App\Http\Controllers;

use App\Http\Requests\TwilioRequest;
use App\Models\Score;
use App\Models\User;
use App\Services\TwilioService;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ScoreController extends Controller
{
    public function store(TwilioRequest $request)
    {
        $twilioService = new TwilioService();
        $data = $request->validated();
        $user = User::where('phone_number', $data['from'])->first();
        if ($user) {
            $todaysScores = $user->scores()->whereDate('created_at', Carbon::today())->get();
            if (count($todaysScores) > 0) {
                $twilioService->sendMessage($user->phone_number, 'Score already submitted today.');

                return \response('Score already submitted for today.', Response::HTTP_CONFLICT);
            }
            $scoreLines = explode(PHP_EOL, $data['body']);
            $scoreCount = count($scoreLines) - 2;
            $score = new Score(['value' => $scoreCount]);
            $score->user()->associate($user);
            $score->save();

            $twilioService->sendMessage($user->phone_number, 'Score has been submitted.');

            return \response('', Response::HTTP_CREATED);
        }

        return \response('User not found', Response::HTTP_NOT_FOUND);
    }
}
