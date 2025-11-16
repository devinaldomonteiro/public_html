<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        echo 'logout';
    }
    public function loginSubmit(Request $request)
    {
        //form validation
        $request->validate(
            //rules
            [
                'text_username' => 'required|email',
                'text_password' => 'required|'
            ],
            [
                'text_username.required' => 'O valor é obrigatório',
                'text_username.email' => 'Username deve ser um e-mail válido',
                'text_password.required' => 'O password é obrigatório'
            ]
        );

        $username = $request->input('text_username');
        
        $password = $request->input('text_password');

        //test database
        try{
            DB::connection()->getPdo();
            echo "Connection OK";
        }catch(\PDOException $e){
            echo "Connection".$e->getMessage();
        }
    }
}
