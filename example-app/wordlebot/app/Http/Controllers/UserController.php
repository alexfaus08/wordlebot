<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(CreateUserRequest $request): User
    {
        $data = $request->validated();
        $user = new User();
        $user->fill($data);
        $user->save();

        return $user;
    }
}
