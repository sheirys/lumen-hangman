<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Libs\Jwt;
use App\Libs\GameEngine;

use App\Auth;
use App\Game;

class GameController extends Controller
{
    public function getSessionList(Request $request) {

        $payload = Jwt::Verify($request->input("jwt"));

        $filter = Game::Where('account_id', '=', $payload->id);

        $game = new GameEngine();

        return response()->json(
            [
                'error' => 0,
                'sessions' => $game->decodeList($filter),
            ],
            Response::HTTP_OK
        );

    }

    public function putSessionNew(Request $request) {

        $payload = Jwt::Verify($request->input("jwt"));

        $game = new GameEngine;
        $game->create($payload->id);
        $game->save();

        return response()->json(
            [
                'error' => 0,
                'session' => $game->decodeSingle(),
            ],
            Response::HTTP_OK
        );

    }

    public function getSessionState(Request $request, $game_id) {

        $payload = Jwt::Verify($request->input("jwt"));

        $filter = Game::Where('account_id', '=', $payload->id)
            ->where('id', '=', $game_id);

        $game = new GameEngine($filter);

        // if game was found
        if(!empty($game)) {

            return response()->json(
                [
                    'error' => 0,
                    'session' => $game->decodeSingle(),
                ],
                Response::HTTP_OK
            );

        }

        // game hasnt been found
        return response()->json(
            [
                'error' => 0,
            ],
            Response::HTTP_NOT_FOUND
        );

    }

    public function postSessionGuess(Request $request, $game_id) {

        $payload = Jwt::Verify($request->input("jwt"));
        $letter = $request->input("letter");

        // no guess on this request
        if(empty($letter)) {

            return response()->json(
                [
                    'error' => 1,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $filter = Game::Where('account_id', '=', $payload->id)
            ->where('id', '=', $game_id);

        $game = new GameEngine($filter);

        // game hasnt been found
        if(empty($game)) {

            return response()->json(
                [
                    'error' => 0,
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        // check maybe game is already over?
        if($game->getState()) {

            return response()->json(
                [
                    'error' => 0,
                    'session' => $game->decodeSingle(),
                ],
                Response::HTTP_LOCKED
            );
        }

        $game->guess($letter);

        return response()->json(
            [
                'error' => 0,
                'session' => $game->decodeSingle(),
            ],
            Response::HTTP_OK
        );

    }

}
