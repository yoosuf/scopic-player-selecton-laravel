<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use App\Http\Resources\PlayerResource;
use App\Http\Requests\TeamSelectionRequest;

class TeamController extends Controller
{
    public function process(TeamSelectionRequest $request)
    {
        $requirements = $request->json()->all();

        $players = collect([]);

        $error  = [];

        foreach ($requirements as $requirement) {

            $bestPlayers = Player::bestPlayersForRequirement($requirement)->get();

            if (count($bestPlayers) != $requirement['numberOfPlayers'])
                $error[] = "Insufficient number of players for position: " . $requirement['position'];

            foreach ($bestPlayers as $bestPlayer) {
                if (!$players->contains('id', $bestPlayer->id)) {
                    $players->push($bestPlayer);
                }
            }
        }

        $result = [];


        foreach ($players as $player) {



            $key = array_search($player->position, array_column($result, 'position'));
            $result[] = $player;
        }


        if (empty($error)) {
            return PlayerResource::collection($result);
        }

        return response()->json(["message" => $error], 400);;


    }
}
