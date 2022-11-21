<?php

namespace App\Http\Controllers;

use App\Http\Requests\TwilioRequest;
use App\Http\Resources\ScoreResource;
use App\Models\Score;
use App\Models\User;
use App\Services\TwilioService;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ScoreController extends Controller
{
    public function index() {
        return ScoreResource::collection(Score::all());
    }

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
            // Split the Wordle score and get the 'Wordle ### X/6' line
            $wordleLines = explode(PHP_EOL, $data['body']);
            $scoreLine = explode(' ', $wordleLines[0])[2];

            // The score is the number before the slash
            $scoreCount = $scoreLine[0];
            $scoreValue = $scoreCount === 'X' ? 7 : $scoreCount;

            $score = new Score(['value' => $scoreValue]);
            $score->user()->associate($user);
            $score->save();

            $twilioService->sendScoreBoardMessage($user->phone_number);

            return \response('', Response::HTTP_CREATED);
        }

        return \response('User not found', Response::HTTP_NOT_FOUND);
    }
}
