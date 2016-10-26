# RESTful Hangman game service
[![Build Status](https://travis-ci.org/sheirys/lumen-hangman.svg?branch=master)](https://travis-ci.org/sheirys/lumen-hangman)

RESTful Hangman's game service written on Lumen microframework.

## Installation

	git clone https://github.com/sheirys/lumen-hangman.git
	cd lumen-hangman
	make install

For testing can be run on built-in PHP server. `make serve` Will start server on `localhost:8000`. Edit `Makefile` for more options or improvise. For testing use `make test` or `cd lumen-hangman/ && phpunit`.

## An example with curl:
First create a new account:

    $ curl --data "email=kaka@makaka.test&pass=makaka" http://localhost:8000/auth/register
    {"error":0,"jwt":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imtha2FAbWFrYWthLnRlc3QiLCJpZCI6NX0.C-wF4fym5Rm6vReyAxkogTrQWItvgFbswn63NH6sENU"}

Our registration point already returns `jwt` for us. Now lets save our jwt within bash session:

    $ JWT="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imtha2FAbWFrYWthLnRlc3QiLCJpZCI6NX0.C-wF4fym5Rm6vReyAxkogTrQWItvgFbswn63NH6sENU"

Let's see if we have any game sessions:

    $ curl -X GET http://localhost:8000/game/sessions?jwt=$JWT
    {"error":0,"sessions":[]}

We didn't play any games yet. So lets start a new game session:

    curl -X PUT http://localhost:8000/game/sessions?jwt=$JWT
    {
        "error":0,
        "session":{
            "session":6,
            "guessed_letters":[],
            "word":["*","*","*","*","*","*","*"],
            "game_over":0,
            "player_won":0
        }
    }

Game engine generated new word for us from 7 letters. Let's guess if there is letter `o`

    curl -X POST "http://localhost:8000/game/sessions/6?jwt=$JWT&letter=o"
    {
        "error":0,
        "session":{
            "session":6,
            "guessed_letters":["o"],
            "word":["*","*","*","o","*","*","*"],
            "game_over":1,
            "player_won":1
        }
    }

Success! There is letter `o` in this word. Lets try `s,a,m,x,p,c,i` letters also.With last guess we lose. That was hard one:

    curl -X POST "http://localhost:8000/game/sessions/6?jwt=$JWT&letter=i"
    {
        "error":0,
        "session":{
            "session":6,
            "guessed_letters":["o","s","a","m","x","p","c","i"],
            "word":["S","i","n","o","p","i","c"],
            "game_over":1,
            "player_won":0
        }
    }


## API documentation
In every JSON response there will be an `error` variable:

+ `{error: 0}` - Request does not have any errors.
+ `{error: 1}` - Request contains some errors and can't be processed propertly.

### Auth/
#### [POST] auth/login
>Login controller. Returns JWT token for other requests. Required data:
>
>- email - Users email.
>- password - Users password.
>
>Returns JSON response:
>
>`{"error":0,"jwt":"eyJ0e....zS4q5M"}`
>
>Possible HTTP responses:
>
>- 200 _OK_ login was successful.
>- 404 _NOT_FOUND_ user hasn't been found.

#### [POST] auth/register
> Creates new user and returns user's JWT token. Required data:
>
>- email - Users email.
>- password - Users password.
>
> Possible HTTP responses:
>
>- 201 _CREATED_ Registration was successfuly.
>- 400 _BAD_REQUEST_ - Request doesn't contain email or password variables.
>- 302 _FOUND_ Email is already in use.

### game/
Every request must contain `jwt` token from `/auth`. In every JSON response there will be an `error` variable:

+ `{error: 0}` - Request does not have any errors.
+ `{error: 1}` - Request contains some errors and can't be processed propertly.

Game `state` contains variables:

+ session - Session ID.
+ guessed_letters - Array of already guess'ed letters.
+ word - Array of letters of word. Known letters are shown and `*` for unknown letters.
+ game_over - Games state.
+ player_won - If `1` player solved the word.

#### [PUT] game/sessions
> Starts a new game and return its state.
>
> Required data:
>
> - jwt - Users JWT token.
>
> Returns JSON response:

	{
		"error":0,
		"session":1,
		"guessed_letters":[],
		"word":["*","*","*","*","*","*","*"],
		"game_over":0,
		"player_won":0
	}

> Possible HTTP responses:
>
> - 200 _OK_ Everythin is ok.
> - 401 _HTTP_UNAUTHORIZED_ Jwt token is invalid or not set.

#### [POST] game/sessions/{session_id}
> Guess'es letter
>
> Required data:
>
> - jwt - Users JWT token.
> - letter - Guessing letter.
>
> Returns JSON response:

	{
		"error":0,
		"session":1,
		"guessed_letters":["e"],
		"word":["*","*","e","*","e","*","e"],
		"game_over":0,
		"player_won":0
	}

> Possible HTTP responses:
>
> - 200 _OK_ Everythin is ok.
> - 401 _HTTP_UNAUTHORIZED_ Jwt token is invalid or not set.
> - 404 _NOT_FOUND_ Game can't be founded or belongs to other user.
> - 423 _LOCKED_ Game can't receive any guess'es anymore.

#### [GET] game/sessions/{session_id}
> Returns game state.
>
> Required data:
>
> - jwt - Users JWT token.
>
> Returns JSON response:

	{
		"error":0,
		"session":1,
		"guessed_letters":["a","b","c","l"],
		"word":["a","b","*","c","*","l","*"],
		"game_over":0,
		"player_won":0
	}

> Possible HTTP responses:
>
> - 200 _OK_ Everythin is ok.
> - 401 _HTTP_UNAUTHORIZED_ Jwt token is invalid or not set.
> - 404 _NOT_FOUND_ Game can't be founded or belongs to other user.

#### [GET] game/sessions
> Returns list of games.
>
> Required data:
>
> - jwt - Users JWT token.
>
> Returns JSON response:

    {
    	"error": 0,
    	"sessions": [<list of games states>]
    }

> Possible HTTP responses:
>
> - 200 _OK_ Everythin is ok.
> - 401 _HTTP_UNAUTHORIZED_ Jwt token is invalid or not set.
