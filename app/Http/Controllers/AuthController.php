<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ================= LOGIN =================
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        if (Auth::attempt([
            $field => $credentials['username'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['username' => 'Akun Anda telah dinonaktifkan.']);
            }

            return redirect()->intended(
                $user->role === 'admin'
                    ? route('admin.dashboard')
                    : route('siswa.dashboard')
            );
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // ================= REGISTER =================
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email'    => 'required|email|unique:users',
            'nis'      => 'required|string|max:20|unique:users',
            'kelas'    => 'required|string|max:20',
            'no_hp'    => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'siswa',
            'nis'      => $validated['nis'],
            'kelas'    => $validated['kelas'],
            'no_hp'    => $validated['no_hp'] ?? null,
        ]);

        Auth::login($user);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Registrasi berhasil!');
    }

    // ================= LOGOUT =================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ================= FORGOT PASSWORD =================
    public function showForgot()
    {
        return view('auth.forgot');
    }

    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $otp = rand(100000, 999999);

        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => Hash::make($otp),
                'expired_at' => now()->addMinutes(5),
                'status' => 'sent'
            ]
        );

        try {
            // ================= EMAIL KE SISWA =================
            Mail::raw("Kode OTP kamu: $otp (aktif 5 menit)", function ($msg) use ($request) {
                $msg->to($request->email)
                    ->subject('OTP Reset Password');
            });

            // ================= EMAIL KE ADMIN =================
            Mail::raw(
                "User dengan email {$request->email} melakukan reset password pada " . now(),
                function ($msg) {
                    $msg->to(env('ADMIN_EMAIL'))
                        ->subject('Notifikasi Reset Password');
                }
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal kirim email: ' . $e->getMessage());
        }

        return redirect()->route('reset.form')
            ->with('email', $request->email);
    }

    // ================= RESET FORM =================
    public function showReset()
    {
        return view('auth.reset');
    }

    // ================= RESET PROCESS =================
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => ['required', 'min:6']
        ]);

        $data = PasswordReset::where('email', $request->email)
            ->where('status', 'sent')
            ->latest()
            ->first();

        if (!$data) {
            return back()->with('error', 'OTP tidak ditemukan');
        }

        if ($data->expired_at < now()) {
            return back()->with('error', 'OTP sudah expired');
        }

        if (!Hash::check($request->otp, $data->otp)) {
            return back()->with('error', 'OTP salah');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        $data->update(['status' => 'used']);

        return redirect()->route('login')
            ->with('success', 'Password berhasil diubah');
    }
}
