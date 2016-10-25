<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

use App\Libs\Jwt;

class AuthTest extends TestCase
{
    public $t_user = [
        'email' => 'test@test.com',
        'pass' => 'test',
    ];

    public function testAuthLoginExisting()
    {

        $this->json('POST', '/auth/login', $this->t_user)
        ->seeStatusCode(Response::HTTP_OK)
        ->seeJson([
            'error' => 0,
            'jwt' => Jwt::createToken($this->t_user['email']),
        ]);
    }

    public function testAuthLoginNonExisting()
    {
        $credentials = [
            'email' => 'nonExistingUser@xxx.com',
            'pass' => 'test',
        ];

        $this->json('POST', '/auth/login', $credentials)
        ->seeStatusCode(Response::HTTP_NOT_FOUND)
        ->seeJson([
            'error' => 1,
        ]);
    }

    // how login will behave when password hasn't been submited
    public function testAuthLoginSubmitErr_1()
    {
        $credentials = [
            'email' => 'nonExistingUser@xxx.com',
        ];

        $this->json('POST', '/auth/login', $credentials)
        ->seeStatusCode(Response::HTTP_NOT_FOUND)
        ->seeJson([
            'error' => 1,
        ]);
    }

    // how login will behave when emil hasn't been submited
    public function testAuthLoginSubmitErr_2()
    {
        $credentials = [
            'pass' => 'test',
        ];

        $this->json('POST', '/auth/login', $credentials)
        ->seeStatusCode(Response::HTTP_NOT_FOUND)
        ->seeJson([
            'error' => 1,
        ]);
    }

    // create new user and returns jwt
    public function testAuthRegisterNewUser()
    {
        $credentials = [
            'email' => 'newTestUser@xxx.com',
            'pass' => 'pass',
        ];

        $this->json('POST', '/auth/register', $credentials)
        ->seeStatusCode(Response::HTTP_CREATED)
        ->seeJson([
            'error' => 0,
            'jwt' => Jwt::createToken($credentials['email']),
        ]);

        $this->seeInDatabase('auth', ['email' => $credentials['email']]);
    }

    // how registration will behave when password hasnt been submited
    public function testAuthRegisterBadUser_1()
    {
        $credentials = [
            'email' => 'nonExistingUser@xxx.com',
        ];

        $this->json('POST', '/auth/register', $credentials)
        ->seeStatusCode(Response::HTTP_BAD_REQUEST)
        ->seeJson([
            'error' => 1,
        ]);
    }

    // how registration will behave when email hasnt been submited
    public function testAuthRegisterBadUser_2()
    {
        $credentials = [
            'pass' => 'xxx',
        ];

        $this->json('POST', '/auth/register', $credentials)
        ->seeStatusCode(Response::HTTP_BAD_REQUEST)
        ->seeJson([
            'error' => 1,
        ]);
    }

    // how registration wil lbehave when creating already existing user
    public function testAuthRegisterUserOverwrite()
    {
        $credentials = [
            'email' => 'test@test.com',
            'pass' => 'pass',
        ];

        $this->json('POST', '/auth/register', $credentials)
        ->seeStatusCode(Response::HTTP_RESERVED)
        ->seeJson([
            'error' => 1,
        ]);

    }

}
