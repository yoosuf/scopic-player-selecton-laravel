<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use App\Http\Requests\PlayerRequest;
use App\Http\Resources\PlayerResource;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.del', ['only' => 'destroy']);
    }


    public function index()
    {
        $players = Player::with('skills')->get();
        return PlayerResource::collection($players);
    }

    public function show($id)
    {
        $player = Player::with('skills')->findOrFail($id);
        return new PlayerResource($player);
    }

    public function store(PlayerRequest $request)
    {
        $player = Player::create($request->validated());
        $player->skills()->createMany($request->validated()['playerSkills']);
        return new PlayerResource($player);
    }

    public function update(PlayerRequest $request,$id)
    {
        $player = Player::findOrFail($id);
        $player->update($request->validated());
        $player->skills()->delete();
        $player->skills()->createMany($request->validated()['playerSkills']);
        $updatedPlayer = Player::with('skills')->findOrFail($player->id);
        return new PlayerResource($updatedPlayer);
    }

    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        $player->delete();
        return response()->json(null, 204);
    }
}
