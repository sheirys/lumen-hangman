<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        DB::table('auth')->insert([
            'id' => 1,
            'email' => 'test@test.com',
            'password' => md5('test'),
        ]);

        DB::table('auth')->insert([
            'id' => 2,
            'email' => 'test2@test.com',
            'password' => md5('test2'),
        ]);

        DB::table('auth')->insert([
            'id' => 3,
            'email' => 'test3@test.com',
            'password' => md5('test3'),
        ]);

        DB::table('game')->insert([
            'id' => 1,
            'account_id' => '1',
            'guessed_letters' => json_encode(['a', 'b', 'c']),
            'word' => json_encode(['a','b','*','c','*','*','*']),
            'answer' => json_encode(['a','b','e','c','e','l','e']),
            'game_over' => 0,
            'player_won' => 0,
        ]);

        DB::table('game')->insert([
            'id' => 2,
            'account_id' => '1',
            'guessed_letters' => json_encode(['a', 'o', 't']),
            'word' => json_encode(['*','a','*','*','o','t']),
            'answer' => json_encode(['c','a','r','r','o','t']),
            'game_over' => 0,
            'player_won' => 0,
        ]);

        DB::table('game')->insert([
            'id' => 3,
            'account_id' => '1',
            'guessed_letters' => json_encode(['a', 'b', 'c']),
            'word' => json_encode(['a','b','c']),
            'answer' => json_encode(['a','b','c']),
            'game_over' => 1,
            'player_won' => 1,
        ]);

        DB::table('game')->insert([
            'id' => 4,
            'account_id' => '1',
            'guessed_letters' => json_encode(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h']),
            'word' => json_encode(['*','*','*']),
            'answer' => json_encode(['z','z','z']),
            'game_over' => 1,
            'player_won' => 0,
        ]);
    }
}
