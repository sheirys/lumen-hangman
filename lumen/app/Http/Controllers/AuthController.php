<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Libs\Jwt;
use App\Auth;

class AuthController extends Controller
{
    public function postLogin(Request $request) {

        $email = $request->input("email");
        $pass = $request->input("pass");

        $auth = Auth::where('email', '=', $email)
            ->where('password', '=', md5($pass))
            ->first();

        if(!empty($auth)) {

            return response()->json(
                [
                    'error' => 0,
                    'jwt' => Jwt::createToken($email),
                ],
                Response::HTTP_OK
            );

        }

        return response()->json(
            [
                'error' => 1
            ],
            Response::HTTP_NOT_FOUND
        );

    }

}
