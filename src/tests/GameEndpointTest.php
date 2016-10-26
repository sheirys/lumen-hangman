<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

use App\Libs\Jwt;

define('T_GOOD_EMAIL', "test@test.com");
define('T_BAD_EMAIL', "test2@test.com");
define('T_GOOD_PASS', "test");


class GameTest extends TestCase
{

    public function testGameSessionList()
    {

        $data = [
            'jwt' => Jwt::createToken(T_GOOD_EMAIL),
        ];

        $this->json('GET', '/game/sessions', $data)
        ->seeStatusCode(Response::HTTP_OK)
        ->seeJson([
            'error' => 0,
            'sessions' => [
                [
                    'session' => 1,
                    'guessed_letters' => ['a', 'b', 'c'],
                    'word' => ['a','b','*','c','*','*','*'],
                    'game_over' => 0,
                    'player_won' => 0,
                ],
                [
                    'session' => 2,
                    'guessed_letters' => ['a', 'o', 't'],
                    'word' => ['*','a','*','*','o','t'],
                    'game_over' => 0,
                    'player_won' => 0,
                ],
                [
                    'session' => 3,
                    'guessed_letters' => ['a', 'b', 'c'],
                    'word' => ['a','b','c'],
                    'game_over' => 1,
                    'player_won' => 1,
                ],
                [
                    'session' => 4,
                    'guessed_letters' => ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
                    'word' => ['*','*','*'],
                    'game_over' => 1,
                    'player_won' => 0,
                ],
            ],
        ]);
    }

    // should return empty sessions list
    public function testGameSessionList_Bad()
    {

        $data = [
            'jwt' => Jwt::createToken(T_BAD_EMAIL),
        ];

        $this->json('GET', '/game/sessions', $data)
        ->seeStatusCode(Response::HTTP_OK)
        ->seeJson([
            'error' => 0,
            'sessions' => [],
        ]);
    }

    // how auth will behave if we post bad JWT?
    public function testGameSessionCheckJWT()
    {
        $data = [
            'jwt' => 'this.jwt.is.wrong.in.so.many.ways'
        ];

        $this->json('GET', '/game/sessions', $data)
        ->seeStatusCode(Response::HTTP_UNAUTHORIZED);
    }

    public function testGameSessionState()
    {
        $data = [
            'jwt' => Jwt::createToken(T_GOOD_EMAIL),
        ];

        $this->json('GET', '/game/sessions/1', $data)
        ->seeStatusCode(Response::HTTP_OK)
        ->seeJson([
            'error' => 0,
            'session' => 1,
            'guessed_letters' => ['a', 'b', 'c'],
            'word' => ['a','b','*','c','*','*','*'],
            'game_over' => 0,
            'player_won' => 0,
        ]);
    }

    public function testGameSessionGuess()
    {
        $data = [
            'jwt' => Jwt::createToken(T_GOOD_EMAIL),
            'letter' => 'l',
        ];

        $this->json('POST', '/game/sessions/1', $data)
        ->seeStatusCode(Response::HTTP_OK)
        ->seeJson([
            'error' => 0,
            'session' => 1,
            'guessed_letters' => ['a', 'b', 'c', 'l'],
            'word' => ['a','b','*','c','*','l','*'],
            'game_over' => 0,
            'player_won' => 0,
        ]);

        $this->seeInDatabase('game', [
            'id' => '1',
            'guessed_letters' => json_encode(['a', 'b', 'c', 'l']),
            'word' => json_encode(['a','b','*','c','*','l','*']),
            'game_over' => 0,
            'player_won' => 0,
        ]);

        $this->json('POST', '/game/sessions/4', $data)
        ->seeStatusCode(Response::HTTP_LOCKED)
        ->seeJson([
            'error' => 0,
            'session' => 4,
            'guessed_letters' => ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
            'word' => ['*','*','*'],
            'game_over' => 1,
            'player_won' => 0,
        ]);

    }

    public function testGameSessionNew() {

        $this->json('PUT', '/game/sessions', ['jwt' => Jwt::createToken(T_GOOD_EMAIL)])
        ->seeStatusCode(Response::HTTP_OK);

        $this->seeInDatabase('game', [
            'id' => '5',
            'account_id' => 1,
            'guessed_letters' => json_encode([]),
            'game_over' => 0,
            'player_won' => 0,
        ]);

    }

}
