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

        // get all the users from the database
        // $users = User::all()->toArray();
        // echo "<pre>";
        // print_r($users);

        // as an object instance of the model's class
        // $userModel = new User();
        // $users = $userModel->all()->toArray();
        // echo "<pre>";
        // print_r($users);

        //check if user exists
        $user = User::where('username', $username)
                ->where('deleted_at', null)
                ->first();
        if(!$user){
            return redirect()->back()->withInput()->with('loginError', 'Username ou Password incorreto.');
        }
        //check o password
        if(!password_verify($password, $user->password)){
            return redirect()->back()->withInput()->with('loginError', 'Username ou Password incorreto.');  
        }

        //update last login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // login user
        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]

        ]);

        echo "Login com sucesso !";
        print_r($user);        
    }
}
