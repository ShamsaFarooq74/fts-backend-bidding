<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\api\ResponseController;
use Illuminate\Support\Facades\Validator;


class AuthenticatedSessionController extends ResponseController
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }
      public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Sorry! Might be required fields are not found or empty.", $validator->errors()->all());
        }


        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            $error = "Invalid credentials! Please try again";
            return $this->sendError(0, $error, null, 401);
        }
        $user = $request->user();
        $userId = Auth::user()->id;
        $user['token'] = $user->createToken('token')->accessToken;
      
        if ($user) {
            // $user["notificationCount"] = Notification::where("notification_type",'!=' , "Ticket")->where("notification_type",'!=' , "Custom")->where(['read_status'=>'N','user_id' => $user->id])->count();
            if ($user->is_active == 'N') {
                $status = -1;
                $message = "Sorry! Your account is blocked. You cant login. ";
                return $this->sendResponse($status, $message, $user);
            }else{
                 $status = 1;
                 $message = "User Login Successfully ";
                 return $this->sendResponse($status,$message, $user);
            }

        } else {
            $message = "Login unsuccessful";
            return $this->sendError(0, $message);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
       $user = $request->user();
       $user->token()->revoke();
       $status = 1;
       $message = "Successfully logged out";
       return $this->sendResponse($status,$message, 400);
    }
}
