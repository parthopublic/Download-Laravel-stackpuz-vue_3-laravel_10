<?php

namespace App\Http\Controllers\Auth;

use App\Util;
use App\Models\UserAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest', ['except' => ['user','logout']]);
    }

    protected function username()
    {
        return 'name';
    }

    protected function credentials()
    {        
        return [ $this->username() => request()->name, 'password' => request()->password, 'active' => 1 ];
    }

    protected function login()
    {
        if (!$token = auth()->attempt($this->credentials(request()))) {
            return response()->json(['message' => 'Invalid credentials'], 400);
        }
        return response()->json([
            'token' => $token,
            'user' => $this->getUser()
        ]);
    }
    
    protected function user()
    {
        return response()->json($this->getUser());
    }

    protected function getUser()
    {
        $user = Auth::user();
        return [
            'name' => $user->name,
            'menu' => $user->getMenu()
        ];
    }

    protected function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out']);
    }

    protected function resetPassword() {
        return view('auth.resetPassword');
    }

    protected function resetPasswordPost() {
        $email = request()->input('email');
        $user = UserAccount::query()
            ->select('useraccount.id')
            ->where('useraccount.email', $email)
            ->first();
        if ($user) {
            $token = Str::random(40);
            $user->update([ 'password_reset_token' => $token ]);
            Util::sentMail('RESET', $email, $token);
            return view('auth.resetPassword', [ 'success' => true ]);
        }
        else {
            return view('auth.resetPassword', [ 'error' => true ]);
        }
    }

    protected function changePassword($token) {
        $user = UserAccount::query()
            ->select('useraccount.id')
            ->where('useraccount.password_reset_token', $token)
            ->first();
        if ($user) {
            return view('auth.changePassword', [ 'token' => $token ]);
        }
        else {
            abort(403);
        }
    }

    protected function changePasswordPost($token) {
        $user = UserAccount::query()
            ->select('useraccount.id')
            ->where('useraccount.password_reset_token', $token)
            ->first();
        if ($user) {
            $user->update([
                'password' => Hash::make(request()->input('password')),
                'password_reset_token' => null,
            ]);
            return view('auth.changePassword', [ 'success' => true, 'token' => $token ]);
        }
        else {
            return view('auth.changePassword', [ 'error' => true, 'token' => $token ]);
        }
    }
}