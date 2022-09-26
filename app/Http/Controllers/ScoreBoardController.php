<?php

namespace App\Http\Controllers;

use App\Http\Requests\TwilioRequest;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Http\Response;

class ScoreBoardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(TwilioRequest $request)
    {
        $data = $request->validated();
        $user = User::where('phone_number', $data['from'])->first();

        if ($user) {
            $twilioService = new TwilioService();
            $message = $twilioService->sendScoreBoardMessage($user->phone_number);

            return \response($message, Response::HTTP_OK);
        }

        return \response('User not found', Response::HTTP_NOT_FOUND);
    }
}
