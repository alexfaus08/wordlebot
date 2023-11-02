<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
    ];

    protected $hidden = ['phone_number'];

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function families(): BelongsToMany
    {
        return $this->belongsToMany(Family::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->email === 'alexfaus08@gmail.com';
    }
}
