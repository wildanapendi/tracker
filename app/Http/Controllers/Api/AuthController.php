<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrasi user baru via API.
     *
     * Endpoint ini membuat akun baru, mem-fire event Registered (yang men-trigger
     * inisialisasi ThesisProfile & Milestone default), lalu otomatis login user.
     * Response: 201 JSON dengan data user dan cookie laravel_session.
     */
    public function register(Request $request, CreateNewUser $creator): JsonResponse
    {
        $user = $creator->create($request->all());

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan verifikasi email Anda.',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'note' => 'Cookie laravel_session telah di-set. Sertakan cookie ini pada setiap request berikutnya.',
        ], 201);
    }

    /**
     * Login via API dan kembalikan data session dalam bentuk JSON.
     *
     * Endpoint ini dirancang khusus untuk REST API client (Postman, Insomnia, cURL)
     * dan TIDAK menggunakan CSRF atau Livewire. Setelah login berhasil,
     * cookie `laravel_session` akan tersimpan otomatis di client.
     */
    public function login(Request $request): JsonResponse
    {
        // Validasi input — gagal validasi akan return 422 JSON
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Coba autentikasi dengan session guard bawaan Laravel
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password yang dimasukkan salah.'],
            ]);
        }

        // Regenerate session ID untuk mencegah session fixation attack
        $request->session()->regenerate();

        $user = Auth::user();

        return response()->json([
            'message' => 'Login berhasil.',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'note' => 'Cookie laravel_session telah di-set. Sertakan cookie ini pada setiap request berikutnya.',
        ], 200);
    }

    /**
     * Logout dari session aktif dan kembalikan konfirmasi JSON.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout berhasil. Session telah dihapus.',
        ], 200);
    }

    /**
     * Cek status autentikasi user saat ini.
     *
     * Route sudah dilindungi middleware auth:web,
     * jadi Auth::user() pasti ada di sini.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'is_authenticated' => true,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }
}
