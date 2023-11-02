<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\Score;
use App\Models\User;
use App\Services\FamilyScoreBoardGeneratorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_family_daily_scoreboards()
    {
        $family1 = Family::factory()->create(['name' => 'Family 1']);
        $family2 = Family::factory()->create(['name' => 'Family 2']);

        $tofu = User::factory()->create(['name' => 'Tofu']);
        $beyond = User::factory()->create(['name' => 'Beyond']);
        $tempeh = User::factory()->create(['name' => 'Tempeh']);

        $tofu->families()->attach($family1);
        $tofu->families()->attach($family2);
        $tempeh->families()->attach($family1);
        $tempeh->families()->attach($family2);
        $beyond->families()->attach($family1);

        Score::factory()->withUser($tofu)->create(['value' => 3, 'created_at' => Carbon::now()]);

        $scoreboardGen = new FamilyScoreBoardGeneratorService($tofu);
        $expScoreboard = 'Family 1 Scoreboard:
1st Tofu: 3
Did not play yet: Tempeh and Beyond
---
Family 2 Scoreboard:
1st Tofu: 3
Did not play yet: Tempeh';

        $scoreboard = $scoreboardGen->getDailyScoreboardMessagesForAllFamilies();
        $this->assertEquals($expScoreboard, $scoreboard);
    }

    public function test_family_daily_scoreboards_empty()
    {
        $family1 = Family::factory()->create(['name' => 'Family 1']);
        $family2 = Family::factory()->create(['name' => 'Family 2']);

        $tofu = User::factory()->create(['name' => 'Tofu']);
        $beyond = User::factory()->create(['name' => 'Beyond']);
        $tempeh = User::factory()->create(['name' => 'Tempeh']);

        $tofu->families()->attach($family1);
        $tofu->families()->attach($family2);
        $tempeh->families()->attach($family1);
        $tempeh->families()->attach($family2);
        $beyond->families()->attach($family1);

        $scoreboardGen = new FamilyScoreBoardGeneratorService($tofu);
        $expScoreboard = 'Family 1 Scoreboard:
No one has played today :(
---
Family 2 Scoreboard:
No one has played today :(';

        $scoreboard = $scoreboardGen->getDailyScoreboardMessagesForAllFamilies();
        $this->assertEquals($expScoreboard, $scoreboard);
    }

    public function test_correct_family_weekly_scoreboard()
    {
        Carbon::setTestNow(Carbon::create(2023, 8, 21, 20, 47));

        $family1 = Family::factory()->create(['name' => 'Family 1']);
        $family2 = Family::factory()->create(['name' => 'Family 2']);

        $user1 = User::factory()->create(['name' => 'Tofu']);
        $user2 = User::factory()->create(['name' => 'Beyond']);
        $user3 = User::factory()->create(['name' => 'Tempeh']);

        $user1->families()->attach($family1);
        $user1->families()->attach($family2);
        $user2->families()->attach($family1);
        $user2->families()->attach($family2);
        $user3->families()->attach($family1);

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

        $expectedScoreboard = 'Family 1 Last Week\'s Scoreboard:
1st Tofu: 28
2nd Beyond: 30
3rd Tempeh: 31

---
Family 2 Last Week\'s Scoreboard:
1st Tofu: 28
2nd Beyond: 30
';
        $scoreboardGen = new FamilyScoreBoardGeneratorService($user1);
        $scoreboard = $scoreboardGen->getWeeklyScoreboardMessagesForAllFamilies();

        $this->assertEquals($expectedScoreboard, $scoreboard);
    }
}
