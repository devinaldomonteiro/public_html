<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        //logout
        session()->forget('user');
        return redirect()->to('/login');
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

        $user = User::where('username', $username)
                    ->where('deleted_at',NULL)
                    ->first();

        if(!$user){
            return redirect()->back()->withInput()->with('loginError', 'Nome de usuario ou login incorretos');
        }

         if(!password_verify($password, $user->password)){
            return redirect()->back()->withInput()->with('loginError', 'Nome de usuario ou login incorretos');
        }

        //update last login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // login user
        session([
            'user'=> [
                'id'=> $user->id,
                'username'=> $user->username
            ]
        ]);

        return view('home');
         
    }
}
