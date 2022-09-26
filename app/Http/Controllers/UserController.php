<?php

namespace App\Http\Controllers;

use App\Http\Requests\TwilioRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(TwilioRequest $request): User
    {
        $data = $request->validated();
        $user = new User(['name' => $data['body'], 'phone_number' => $data['from']]);
        $user->save();

        return $user;
    }
}
