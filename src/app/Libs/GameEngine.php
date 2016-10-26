<?php
namespace App\Libs;

use App\Auth;
use App\Game;

class GameEngine {

    private $instance;

    public function __construct($filter = NULL) {

        if($filter !== NULL) {

            $this->instance = $filter->get();

            // overwrite instance with single record if possible
            if($this->instance->count() === 1) {
                $this->instance = $this->instance->first();
            }

        }
    }

    // decodes multiple games from database to user readable JSON array
    public function decodeList($games) {

        $sessions = [];

        foreach($games as $game) {
            $sessions[] = $this->decodeSingle($game);
        }

        return $sessions;

    }

    // decodes single game from database to user readable JSON array
    public function decodeSingle($game = NULL) {

        if($game === NULL)
            $game = $this->instance;

        // when we pull out games from database
        // records are with answers, so we need to
        // recreate array without answers & timestamps
        // also Laravel response()->json(..) has bug
        // about INT conversions and tests are strict about it,
        // so we need (int) inline conversion. Read more:
        // https://github.com/laravel/framework/issues/11068

        if($game->game_over) {
            $word = $game->answer;
        } else {
            $word = $game->word;
        }

        return [
            'session' => $game->id,
            'guessed_letters' => json_decode($game->guessed_letters),
            'word' => json_decode($word),
            'game_over' => (int)$game->game_over,
            'player_won' => (int)$game->player_won,
        ];

    }

    // creates new game session
    public function create($account_id) {

        $game = new Game;

        // random word generator

        @$random_word = file_get_contents("http://www.setgetgo.com/randomword/get.php");

        if(!$random_word) {
            // fallback if random generator does not love us
            // TODO: read from file
            $word_list = ["sheep", "carrot", "bunny", "pie"];
            $random_word = $word_list[array_rand($word_list)];
        }

        $answer = str_split($random_word);

        foreach($answer as $char)
            $word[] = "*";

        $game->account_id = $account_id;
        $game->word = json_encode($word);
        $game->answer = json_encode($answer);
        $game->guessed_letters = json_encode([]);
        $game->player_won = 0;
        $game->game_over = 0;

        $this->instance = $game;

        return $this;

    }

    // letter guessing logic
    public function guess($letter) {

        $game = $this->instance;

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

        if(array_search('*', $word) === FALSE) {
            $game->player_won = 1;
            $game->game_over = 1;
        }

        $game->guessed_letters = json_encode($guessed_letters);
        $game->word = json_encode($word);

        $game->save();

    }

    public function save() {
        $this->instance->save();
    }

    public function getState() {
        return $this->instance->game_over;
    }

}
