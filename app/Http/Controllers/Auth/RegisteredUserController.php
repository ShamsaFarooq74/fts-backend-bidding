<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\api\ResponseController;

class RegisteredUserController extends ResponseController
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
    
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'confirm_password' => 'required|same:password',
        ]);
          
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);
          
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
        
    }
       public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
           return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $request['password'] = Hash::make($request['password']);
        $user = User::create($request->except(["confirm_password"]));
        if ($user) {
            $user['token'] = $user->createToken('token')->accessToken;
            $message = "Registration successful";
            $user = User::find($user->id);
            return $this->sendResponse(1, $message, $user);
        } else {
            $error = "Something went wrong! Please try again";
            return $this->sendError(0, $error, null, 401);
        }

    }

}
