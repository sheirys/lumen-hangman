<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class ApiTest extends TestCase
{

    public function testAuthLoginExisting()
    {

        $credentials = [
            'email' => 'test@test.com',
            'pass' => 'test',
        ];
        
        $this->json('POST', '/auth/login', $credentials)
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

}
