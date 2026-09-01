<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->must_change_password) {
                return redirect()->route('password.change.show');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = $request->input('login');
        $password = $request->input('password');

        // Check if user exists by Email, Phone Number, OR Name. A phone number (or name)
        // can be shared by more than one account (e.g. siblings using a parent's number,
        // or two staff members with the same name), so every matching account whose
        // password checks out is a valid candidate.
        $matches = User::where('email', $loginInput)
                    ->orWhere('phone', $loginInput)
                    ->orWhereRaw('LOWER(name) = ?', [strtolower($loginInput)])
                    ->get()
                    ->filter(fn ($u) => Hash::check($password, $u->password))
                    // A student marked as "Left" keeps their historical records but
                    // is blocked from logging back in.
                    ->reject(fn ($u) => $u->isStudent() && $u->studentProfile && ! $u->studentProfile->isActive());

        if ($matches->isEmpty()) {
            return back()->withErrors([
                'login' => 'The provided credentials do not match our student/staff records.',
            ])->onlyInput('login');
        }

        if ($matches->count() > 1) {
            $request->session()->put('login_candidates', $matches->pluck('id')->all());
            return redirect()->route('login.choose');
        }

        return $this->completeLogin($request, $matches->first());
    }

    public function showChooseAccount(Request $request)
    {
        $ids = $request->session()->get('login_candidates', []);

        if (empty($ids)) {
            return redirect()->route('login');
        }

        $accounts = User::whereIn('id', $ids)->with('studentProfile.course')->get();

        return view('auth.choose-account', compact('accounts'));
    }

    public function chooseAccount(Request $request, User $user)
    {
        $ids = $request->session()->get('login_candidates', []);

        if (! in_array($user->id, $ids, true)) {
            abort(403);
        }

        $request->session()->forget('login_candidates');

        return $this->completeLogin($request, $user);
    }

    private function completeLogin(Request $request, User $user)
    {
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Force password change if student/user is logging in for the first time
        if ($user->must_change_password) {
            return redirect()->route('password.change.show')->with('info', 'Please set and confirm your new password to proceed to your dashboard.');
        }

        return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
    }

    public function showChangePassword()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Your password has been updated successfully! Welcome to your dashboard.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
