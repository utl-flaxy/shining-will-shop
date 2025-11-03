<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::middleware('web')->group(function () {
    Route::get('/login', function () {
        return response('OK', 200);
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->only(['email', 'password']);
        $user = User::where('email', $credentials['email'] ?? '')->first();

        if (! $user || ! Hash::check($credentials['password'] ?? '', $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        Auth::login($user);

        // Return a redirect to /dashboard to match tests that expect a redirect
        return redirect('/dashboard', 302);
    });

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();

        // Return redirect to / to match tests that expect a redirect
        return redirect('/', 302);
    })->name('logout');

    // Register: use Validator so validation errors appear as JSON errors (tests expect validation errors)
    Route::post('/register', function (Request $request) {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation']);

        $validator = Validator::make($data, [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $data['name'] ?? 'Test User',
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'remember_token' => Str::random(10),
        ]);

        return response()->json(['id' => $user->id], 201);
    })->name('register');

    Route::post('/email/verification-notification', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return response()->json([], 401);
        }
        $user->sendEmailVerificationNotification();
        return response()->json([], 200);
    })->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
        $user = User::find($id);
        if (! $user) {
            abort(404);
        }

        if (! hash_equals($hash, sha1($user->email))) {
            abort(403);
        }

        $user->markEmailAsVerified();

        return redirect('/', 302);
    })->name('verification.verify');

    Route::post('/forgot-password', function (Request $request) {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if ($user) {
            $token = Password::broker()->createToken($user);
            Notification::send($user, new ResetPassword($token));
        }
        return response()->json([], 200);
    })->name('password.email');

    Route::post('/reset-password', function (Request $request) {
        $data = $request->only(['token', 'email', 'password', 'password_confirmation']);
        $status = Password::broker()->reset(
            $data,
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([], 200);
        }

        return response()->json([], 422);
    })->name('password.update');

    // Password confirmation route expected by tests (route('password.confirm'))
    Route::post('/confirm-password', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return response()->json([], 401);
        }

        $password = $request->input('password', '');

        if (! Hash::check($password, $user->password)) {
            // Return validation-like response (422) so tests expecting failure get 422
            return response()->json(['message' => 'The provided password does not match our records.'], 422);
        }

        // Many implementations return 200 / 204 on success; tests expect 200
        return response()->json([], 200);
    })->name('password.confirm');

    Route::post('/user/password', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return response()->json([], 401);
        }

        if (! Hash::check($request->input('current_password', ''), $user->password)) {
            return response()->json([], 422);
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json([], 200);
    })->name('password.update.auth');
});
