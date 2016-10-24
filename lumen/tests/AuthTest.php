<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    public $t_user = [
        'email' => 'test@test.com',
        'pass' => 'test',
    ];

    public function testAuthLoginExisting()
    {

        $this->json('POST', '/auth/login', $this->t_user)
        ->seeStatusCode(200)
        ->seeJson([
            'error' => false,
            'login' => true,
        ]);
    }

    public function testAuthLoginNonExisting()
    {
        $credentials = [
            'email' => 'nonExistingUser@xxx.com',
            'pass' => 'test',
        ];

        $this->json('POST', '/auth/login', $credentials)
        ->seeStatusCode(401)
        ->seeJson([
            'error' => true,
            'login' => false,
        ]);
    }

    // how login will behave when password hasn't been submited
    public function testAuthLoginSubmitErr_1()
    {
        $credentials = [
            'email' => 'nonExistingUser@xxx.com',
        ];

        $this->json('POST', '/auth/login', $credentials)
        ->seeStatusCode(401)
        ->seeJson([
            'error' => true,
            'login' => false,
        ]);
    }

    // how login will behave when emil hasn't been submited
    public function testAuthLoginSubmitErr_2()
    {
        $credentials = [
            'pass' => 'test',
        ];

        $this->json('POST', '/auth/login', $credentials)
        ->seeStatusCode(401)
        ->seeJson([
            'error' => true,
            'login' => false,
        ]);
    }

    public function testAuthRegisterNewUser()
    {

        $this->json('POST', '/auth/register', $this->t_user)
        ->seeStatusCode(200)
        ->seeJson([
            'error' => false,
            'success' => true,
        ]);
    }

    // how registration will behave when password hasnt been submited
    public function testAuthRegisterBadUser_1()
    {
        $credentials = [
            'email' => 'nonExistingUser@xxx.com',
        ];

        $this->json('POST', '/auth/register', $credentials)
        ->seeStatusCode(400)
        ->seeJson([
            'error' => true,
            'success' => false,
        ]);
    }

    // how registration will behave when email hasnt been submited
    public function testAuthRegisterBadUser_2()
    {
        $credentials = [
            'pass' => 'xxx',
        ];

        $this->json('POST', '/auth/register', $credentials)
        ->seeStatusCode(400)
        ->seeJson([
            'error' => true,
            'success' => false,
        ]);
    }

}
