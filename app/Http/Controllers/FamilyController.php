<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Carbon\Carbon;
use Inertia\Inertia;

class FamilyController extends Controller
{
    public function show(Family $family): \Inertia\Response
    {
        $today = Carbon::today();
        $todaysScores = $family->getSortedScoresForDate($today);

        return Inertia::render('Family/Show', [
            'name' => $family->name,
            'scores' => $todaysScores
        ]);
    }
}
