<?php

namespace Tests\Feature;

use App\Models\Score;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TwilioTest extends TestCase
{
    use WithoutMiddleware;
    use RefreshDatabase;
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_it_stores_score_correctly(): void
    {
        $user = User::factory()->create();
        $wordleScore = 'Wordle 887 3/6

⬛⬛⬛🟩⬛
⬛⬛🟨🟩⬛
🟩🟩🟩🟩🟩';
        $textMessage =  ['from' => $user->phone_number, 'body' => $wordleScore];
        $response = $this->post('/api/score', $textMessage);

        $response->assertStatus(201);
        $score = Score::where('user_id', $user->id)->first();
        $this->assertEquals($score->full_score, $wordleScore);
    }
}
