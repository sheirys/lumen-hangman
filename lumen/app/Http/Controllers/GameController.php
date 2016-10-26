<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Libs\Jwt;
use App\Auth;
use App\Game;

class GameController extends Controller
{
    public function getSessionList(Request $request) {

        $payload = Jwt::Verify($request->input("jwt"));

        $games = Game::Where('account_id', '=', $payload->id)
            ->get();

        // when we pull out games from database
        // records are with answers, so we need to
        // recreate array without answers & timestamps
        // also Laravel response()->json(..) has bug
        // about INT conversions and tests are strict about it,
        // so we need (int) inline conversion. Read more:
        // https://github.com/laravel/framework/issues/11068
        $sessions = [];

        foreach($games as $game) {
            $sessions[] = [
                'session' => $game->id,
                'guessed_letters' => json_decode($game->guessed_letters),
                'word' => json_decode($game->word),
                'game_over' => (int)$game->game_over,
                'player_won' => (int)$game->player_won,
            ];
        }

        return response()->json(
            [
                'error' => 0,
                'sessions' => $sessions,
            ],
            Response::HTTP_OK
        );

    }

    public function putSessionNew(Request $request) {

    }

    public function getSessionState(Request $request, $game_id) {

        $payload = Jwt::Verify($request->input("jwt"));

        $game = Game::Where('account_id', '=', $payload->id)
            ->where('id', '=', $game_id)
            ->first();

        // if game was found
        if(!empty($game)) {

            // when we pull out games from database
            // records are with answers, so we need to
            // recreate array without answers & timestamps
            // also Laravel response()->json(..) has bug
            // about INT conversions and tests are strict about it,
            // so we need (int) inline conversion. Read more:
            // https://github.com/laravel/framework/issues/11068

            return response()->json(
                [
                    'error' => 0,
                    'session' => $game->id,
                    'guessed_letters' => json_decode($game->guessed_letters),
                    'word' => json_decode($game->word),
                    'game_over' => (int)$game->game_over,
                    'player_won' => (int)$game->player_won,
                ],
                Response::HTTP_OK
            );

        }

        // game hasnt been found
        return response()->json(
            [
                'error' => 1,
            ],
            Response::HTTP_NOT_FOUND
        );

    }

    public function postSessionGuess(Request $request, $game_id) {

    }
}
