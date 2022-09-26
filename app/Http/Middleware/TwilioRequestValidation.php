<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Twilio\Security\RequestValidator;

class TwilioRequestValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Be sure TWILIO_AUTH_TOKEN is set in your .env file.
        // You can get your authentication token in your twilio console https://www.twilio.com/console
        $requestValidator = new RequestValidator(env('TWILIO_AUTH_TOKEN'));

        $requestData = $request->toArray();

        // Switch to the body content if this is a JSON request.
        if (array_key_exists('bodySHA256', $requestData)) {
            $requestData = $request->getContent();
        }

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
    }
}
