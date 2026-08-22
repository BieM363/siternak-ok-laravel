<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cow;
use App\Models\Calf;
use App\Models\PregnancyRecord;

class AuthController extends Controller
{
    /**
     * Tampilan Landing Page / Home Utama (Disesuaikan Per-Role)
     */
    public function showHome()
    {
        $user = Auth::user();

        if ($user && $user->isAdminKabupaten()) {
            $totalCows = Cow::where('regency_id', $user->regency_id)->count();
            $totalCalves = Calf::whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id))->count();
            $totalIb = PregnancyRecord::whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id))->count();
            $totalFarmers = \App\Models\Farmer::where('regency_id', $user->regency_id)->count();
            $totalInseminators = \App\Models\User::where('role', 'operator')->where('regency_id', $user->regency_id)->count();
        } elseif ($user && $user->isOperator()) {
            $totalCows = Cow::forOperator($user)->count();
            $totalCalves = Calf::whereHas('cow', fn($q) => $q->forOperator($user))->count();
            $totalIb = PregnancyRecord::where('operator_id', $user->id)->count();
            $totalFarmers = \App\Models\Farmer::count();
            $totalInseminators = \App\Models\User::where('role', 'operator')->count();
        } else {
            $totalCows = Cow::count();
            $totalCalves = Calf::count();
            $totalIb = PregnancyRecord::count();
            $totalFarmers = \App\Models\Farmer::count();
            $totalInseminators = \App\Models\User::where('role', 'operator')->count();
        }

        return view('home', compact('totalCows', 'totalCalves', 'totalIb', 'totalFarmers', 'totalInseminators'));
    }

    /**
     * Akses Langsung Publik Mode Pelihat (Read-Only) ke fitur tertentu
     */
    public function guestAccess(Request $request)
    {
        $target = $request->query('target', 'dashboard');
        $validTargets = ['dashboard', 'cows.index', 'reports.index', 'calves.index'];
        if (!in_array($target, $validTargets)) {
            $target = 'dashboard';
        }

        return redirect()->route($target);
    }

    public function showLogin()
    {
        if (Auth::check() && !Auth::user()->isGuest()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function guestLogin(Request $request)
    {
        return $this->guestAccess($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar dari akun pengguna.');
    }
}
