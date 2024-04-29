<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\api\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetLinkController extends ResponseController
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }

      public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()) {
          return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
            
        }
        $email = $request->get('email');
        $userDetails = User::where('email', '=', $email)->selectRaw("id,name")->first();
      
        if (!empty($userDetails)) {
            $otp = Str::random(6);
            return $otp;
            $resetUrl = url("/reset-password/" . $otp);
            Mail::to($email)->send(new ForgotPassword($userDetails->name, $resetUrl));
            User::where('id', '=', $userDetails->id)->update([
                "reset_token" => $otp
            ]);

        } else {
                return $this->sendResponse(0, 'Email does not exist', null);
        }
    }
}
