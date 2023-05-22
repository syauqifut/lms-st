<?php

namespace App\Http\Controllers\Auth;

use App\ForgetPassword;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return Inertia::render('Auth/Password/Email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $emailExist = User::where('email', $request->email)->first();
        if (!$emailExist) {
            return redirect()->back()->withErrors("There's no user with that email in database");
        }
        // dd($emailExist);

        // $token = Str::random(100);
        $token=md5($request->email.date("dmY"));
        // dd($token);
        $date = date("Y-m-d H:i:s", strtotime('-24 hours', time()));
       // dd($date);
        $deldata=ForgetPassword::where('created_at','<', $date)->delete();
       // dd($deldata);

        // Check if user already request password
        $dataExist = ForgetPassword::where('email', $emailExist->email)->first();
        if ($dataExist) {
            $dataExist->update(['token' => $token]);
        } else {
            ForgetPassword::create([
                'email' => $emailExist->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);
        }

        $to_name = $emailExist->fullname;
        $to_email = $emailExist->email;
        $data = [
            "name" => $emailExist->fullname,
            "body" => "Tes body email",
            "link" => route('password.reset_form', ['token' => $token, 'email' => $emailExist->email]),
        ];
        Mail::send("emails.reset-password", $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject("Notifikasi Reset Password");
            $message->from(env("MAIL_FROM_ADDRESS"), "LMS AL-FITHRAH");
        });
        /*
        http://127.0.0.1:8000/password/reset/5bdd8d39141e954ea12c155fd1c7a2f39ce979fd3015cb95a62f8a966016021d?email=tommyrachmadiono%40gmail.com
        */

        return redirect()->route('login')->with('success', 'Berhasil mengirim link reset password');
    }

    public function showResetForm($token, Request $request)
    {
        // dd($token, $request->all());
        $tokenExist = ForgetPassword::where('token', $token)->first();
        if (!$tokenExist) {
            return redirect()->route('login')->withErrors('Token not found');
        }
        // else{
        //     ForgetPassword::where('token', $token)->delete();
        // }
        // dd($tokenExist);

       // $res=ForgetPassword::where('token', $token)->delete();
        $data['dataReset'] = $tokenExist;

        return Inertia::render('Auth/Password/Reset', $data);
    }

    public function resetPassword(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        // dd($user->password);
        $user->password =  bcrypt($request->password);
        $user->save();

        $dataRequestPassword = ForgetPassword::where('email', $request->email)->delete();
        
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        // dd(Auth::attempt($credentials), $credentials);
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        }
    }
}
