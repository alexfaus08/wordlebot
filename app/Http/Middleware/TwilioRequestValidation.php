<?php

namespace App\Http\Middleware;

use App\Http\Requests\TwilioRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Twilio\Security\RequestValidator;

class TwilioRequestValidation
{
    public function handle(TwilioRequest $request, Closure $next)
    {
        if (App::isProduction()) {
            // Be sure TWILIO_AUTH_TOKEN is set in your .env file.
            // You can get your authentication token in your twilio console https://www.twilio.com/console
            $requestValidator = new RequestValidator(env('TWILIO_AUTH_TOKEN'));

            $requestData = $request->toArray();

            $isValid = $requestValidator->validate(
                $request->header('X-Twilio-Signature'),
                $request->fullUrl(),
                $requestData
            );

            if ($isValid) {
                return $next($request);
            } else {
                return new Response('You are not Twilio :(', 403);
            }
        } else {
            return $next($request);
        }
    }
}
