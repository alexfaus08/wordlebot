<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function getSortedScoresForDate(Carbon $date): Collection
    {
        return Score::whereDate('created_at', $date)
            ->whereIn('user_id', $this->users->pluck('id'))
            ->orderBy('value')
            ->get();
    }
}
