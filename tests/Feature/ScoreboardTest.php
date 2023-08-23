<?php

namespace Tests\Feature;

use App\Models\Score;
use App\Models\User;
use App\Services\ScoreBoardGeneratorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreboardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_correct_weekly_scoreboard()
    {
        Carbon::setTestNow(Carbon::create(2023, 8, 21, 20, 47));

        $user1 = User::factory()->create(['name' => 'Tofu']);
        $user2 = User::factory()->create(['name' => 'Beyond']);
        $user3 = User::factory()->create(['name' => 'Tempeh']);
        // first place scores - total: 28
        Score::factory()->withUser($user1)->create(['value' => 4, 'created_at' => '2023-08-14 20:47:50']);
        Score::factory()->withUser($user1)->create(['value' => 4, 'created_at' => '2023-08-15 20:47:50']);
        Score::factory()->withUser($user1)->create(['value' => 4, 'created_at' => '2023-08-16 20:47:50']);
        Score::factory()->withUser($user1)->create(['value' => 4, 'created_at' => '2023-08-17 20:47:50']);
        Score::factory()->withUser($user1)->create(['value' => 4, 'created_at' => '2023-08-18 20:47:50']);
        Score::factory()->withUser($user1)->create(['value' => 4, 'created_at' => '2023-08-19 20:47:50']);
        Score::factory()->withUser($user1)->create(['value' => 4, 'created_at' => '2023-08-20 20:47:50']);

        // second place scores - total: 30
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-14 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-15 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-16 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-17 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-18 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-19 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 6, 'created_at' => '2023-08-20 20:47:50']);

        // third place scores (missed a day) - total: 31
        Score::factory()->withUser($user3)->create(['value' => 4, 'created_at' => '2023-08-14 20:47:50']);
        Score::factory()->withUser($user3)->create(['value' => 4, 'created_at' => '2023-08-15 20:47:50']);
        Score::factory()->withUser($user3)->create(['value' => 4, 'created_at' => '2023-08-16 20:47:50']);
        Score::factory()->withUser($user3)->create(['value' => 4, 'created_at' => '2023-08-17 20:47:50']);
        Score::factory()->withUser($user3)->create(['value' => 4, 'created_at' => '2023-08-18 20:47:50']);
        Score::factory()->withUser($user3)->create(['value' => 4, 'created_at' => '2023-08-19 20:47:50']);

        $expectedScoreboard = 'Last Week\'s Scoreboard:
1st - Tofu 28
2nd - Beyond 30
3rd - Tempeh 31';
        $scoreboardGen = new ScoreBoardGeneratorService();
        $scoreboard = $scoreboardGen->getWeeklyScoreboardMessage();

        $this->assertEquals($expectedScoreboard, $scoreboard);
    }

    public function test_correct_daily_scoreboard()
    {
        Carbon::setTestNow(Carbon::create(2023, 8, 14, 21));

        $user1 = User::factory()->create(['name' => 'Tofu']);
        $user2 = User::factory()->create(['name' => 'Beyond']);
        $user3 = User::factory()->create(['name' => 'Tempeh']);

        Score::factory()->withUser($user1)->create(['value' => 3, 'created_at' => '2023-08-14 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-14 20:47:50']);
        Score::factory()->withUser($user3)->create(['value' => 5, 'created_at' => '2023-08-14 20:47:50']);

        $expectedScoreboard = 'Today\'s Scoreboard:
1st - Tofu 3/6
2nd - Beyond 4/6
3rd - Tempeh 5/6';
        $scoreboardGen = new ScoreBoardGeneratorService();
        $scoreboard = $scoreboardGen->getDailyScoreboardMessage();

        $this->assertEquals($expectedScoreboard, $scoreboard);
    }

    public function test_correct_daily_scoreboard_with_missing_score()
    {
        Carbon::setTestNow(Carbon::create(2023, 8, 14, 21));

        $user1 = User::factory()->create(['name' => 'Tofu']);
        $user2 = User::factory()->create(['name' => 'Beyond']);
        User::factory()->create(['name' => 'Tempeh']);

        Score::factory()->withUser($user1)->create(['value' => 3, 'created_at' => '2023-08-14 20:47:50']);
        Score::factory()->withUser($user2)->create(['value' => 4, 'created_at' => '2023-08-14 20:47:50']);

        $expectedScoreboard = 'Today\'s Scoreboard:
1st - Tofu 3/6
2nd - Beyond 4/6

Not played yet: Tempeh';
        $scoreboardGen = new ScoreBoardGeneratorService();
        $scoreboard = $scoreboardGen->getDailyScoreboardMessage();

        $this->assertEquals($expectedScoreboard, $scoreboard);
    }

    public function test_correct_daily_scoreboard_when_no_one_played()
    {
        Carbon::setTestNow(Carbon::create(2023, 8, 14, 21));

        $expectedScoreboard = 'No one has played today :(';
        $scoreboardGen = new ScoreBoardGeneratorService();
        $scoreboard = $scoreboardGen->getDailyScoreboardMessage();

        $this->assertEquals($expectedScoreboard, $scoreboard);
    }
}
