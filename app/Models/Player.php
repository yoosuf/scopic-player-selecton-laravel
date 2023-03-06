<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Models;

use App\Enums\PlayerPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $name
 * @property PlayerPosition $position
 * @property PlayerSkill $skill
 */
class Player extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'position'
    ];

    protected $casts = [
        'position' => PlayerPosition::class
    ];

    protected $with = ['skills'];


    /**
     * Asociation for SKills Table
     *
     * @return HasMany
     */
    public function skills(): HasMany
    {
        return $this->hasMany(PlayerSkill::class);
    }


    /**
     * Scope function to query BestPlayersForRequirement
     *
     * @param [type] $query
     * @param [type] $requirement
     * @return void
     */
    public function scopeBestPlayersForRequirement($query, $requirement)
    {
        return $query->where('position', $requirement['position'])
            ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
            ->where('player_skills.skill', $requirement['mainSkill'])
            ->orderByDesc('player_skills.value')
            ->take($requirement['numberOfPlayers'])
            ->select('players.*', 'player_skills.skill', 'player_skills.value');
    }

}
