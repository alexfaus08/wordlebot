<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
                'from' => ['required', 'date', 'date_format:m/d/Y'],
                'to' => ['required', 'date', 'date_format:m/d/Y', 'after_or_equal:from']
            ]
        );

        $from = Carbon::createFromFormat('m/d/Y', $data['from'])->setTime(0, 0);
        $to = Carbon::createFromFormat('m/d/Y', $data['to'])->setTime(23, 59, 59, 59);

        $usersWithScores = User::with(['scores' => function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }])->get();

        $scores = $usersWithScores->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total' => $user->scores->sum('value'),
                    'scores' => $user->scores->map(function ($score) {
                        return [
                            'id' => $score->id,
                            'value' => $score->value,
                        ];
                    }),
                ];
            });

        $sorted = array_values(Arr::sort($scores, function (array $value) {
            return $value['total'];
        }));

        return response()->json(['data' => $sorted]);
    }
}
