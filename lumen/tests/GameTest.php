<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class GameTest extends TestCase
{
    public $good_jwt;
    public $bad_jwt;

    public function testGameSessionList()
    {

        $data = [
            'jwt' => $this->good_jwt,
        ];

        $this->json('GET', '/game/sessions', $data)
        ->seeStatusCode(200)
        ->seeJson([
            'error' => false,
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

    public function testGameSessionList_Bad()
    {

        $data = [
            'jwt' => $this->good_jwt,
        ];

        $this->json('GET', '/game/sessions', $data)
        ->seeStatusCode(401)
        ->seeJson([
            'error' => true,
            'sessions' => [],
        ]);
    }

    public function testGameSessionStatus()
    {
        $data = [
            'jwt' => $this->good_jwt,
        ];

        $this->json('GET', '/game/sessions/1', $data)
        ->seeStatusCode(200)
        ->seeJson([
            'error' => false,
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
            'jwt' => $this->good_jwt,
            'letter' => 'l',
        ];

        $this->json('POST', '/game/sessions/1', $data)
        ->seeStatusCode(200)
        ->seeJson([
            'error' => false,
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
        ->seeStatusCode(200)
        ->seeJson([
            'error' => false,
            'session' => 4,
            'guessed_letters' => ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
            'word' => ['*','*','*'],
            'game_over' => 1,
            'player_won' => 0,
        ]);

    }

    public function testGameSessionNew() {

        $this->json('PUT', '/game/sessions', ['jwt' => $this->goof_jwt])
        ->seeStatusCode(200);

        $this->seeInDatabase('game', [
            'id' => '5',
            'account_id' => 1,
            'guessed_letters' => json_encode([]),
            'game_over' => 0,
            'player_won' => 0,
        ]);

    }

}
