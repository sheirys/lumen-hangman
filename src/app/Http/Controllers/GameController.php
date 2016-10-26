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

        $payload = Jwt::Verify($request->input("jwt"));

        // random word generator
        $random_word = file_get_contents("http://www.setgetgo.com/randomword/get.php");

        // fallback if random generator does not love us
        if(empty($random_word)) {

        }

        $game = new Game;

        $answer = str_split($random_word);

        foreach($answer as $char)
            $word[] = "*";

        $game->account_id = $payload->id;
        $game->word = json_encode($word);
        $game->answer = json_encode($answer);
        $game->guessed_letters = json_encode([]);
        $game->player_won = 0;
        $game->game_over = 0;

        $game->save();

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

        $game = Game::Where('account_id', '=', $payload->id)
            ->where('id', '=', $game_id)
            ->first();

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
        if($game->game_over) {

            return response()->json(
                [
                    'error' => 0,
                    'session' => $game->id,
                    'guessed_letters' => json_decode($game->guessed_letters),
                    'word' => json_decode($game->word),
                    'game_over' => (int)$game->game_over,
                    'player_won' => (int)$game->player_won,
                ],
                Response::HTTP_LOCKED
            );
        }

        $guessed_letters = json_decode($game->guessed_letters);
        $word = json_decode($game->word);
        $answer = json_decode($game->answer);

        // maybe letter is already in guessed_letters list?
        if(!in_array($letter,$guessed_letters))
            $guessed_letters[] = $letter;

        // mark guessed letter in $word list
        $keys = array_keys($answer, $letter);
        foreach($keys as $key) {
            //setting '*' to '$letter'
            $word[$key] = $letter;
        }

        if(count($guessed_letters) >= 8)
            $game->game_over = 1;

        if(!array_search('*', $word)) {
            $game->player_won = 1;
            $game->game_over = 1;
        }

        $game->guessed_letters = json_encode($guessed_letters);
        $game->word = json_encode($word);

        $game->save();

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

}
