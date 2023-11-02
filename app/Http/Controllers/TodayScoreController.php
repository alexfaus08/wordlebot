<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScoreResource;
use App\Models\Score;
use Carbon\Carbon;

class TodayScoreController extends Controller
{
    public function __invoke()
    {
        $todaysScores = Score::whereDate('created_at', Carbon::today())->orderBy('value')->get();

        return ScoreResource::collection($todaysScores);
    }
}
