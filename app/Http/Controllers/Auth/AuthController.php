<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

use App\Models\User;

use App\Http\Resources\User\UserResource;

use App\Events\UserRegisteredEvent;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'login',
            'register',
            'validateVerificationToken',
            'sendPasswordResetLink',
            'resetPassword'
        ]]);
    }

    public function login(Request $request)
    {
        // Attempt login with access token
        if ($request->input('access_token')) {
            $accessToken = $request->input('access_token');

            auth()->setToken($accessToken);

            if (auth()->authenticate()) {
                $this->respondWithAccessToken($accessToken);
            }
        }

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only(['email', 'password']);
        $accessToken = auth()->attempt($credentials);

        if (!$accessToken) {
            return response()->json(['errors' => 'Unauthenticated.'], 422);
        }

        return $this->respondWithAccessToken($accessToken);
    }

    public function me()
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => $e->message], 404);
        }

        return response()->json($user);
    }

    public function logout()
    {
        auth()->logout(true);

        return response()->json(['message' => "You've been logged out successfully."]);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'verified' => false,
            'verification_token' => User::generateVerificationToken()
        ]);

        $credentials = $request->only(['email', 'password']);
        $accessToken = auth()->attempt($credentials);

        if (!$accessToken) {
            return response()->json(['error' => 'Invalid email/password combination.'], 422);
        }

        UserRegisteredEvent::dispatch($user);

        return $this->respondWithAccessToken($accessToken);
    }

    public function refreshAccessToken()
    {
        return $this->respondWithAccessToken(auth()->refresh(true, true));
    }

    protected function respondWithAccessToken($token)
    {
        return (new UserResource(auth()->user()))->additional([
            'meta' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60 // 1 hour
            ]
        ]);
    }

    public function validateVerificationToken($verificationToken)
    {
        $user = User::where('verification_token', $verificationToken)->first();

        if (is_null($user)) {
            return response()->json(['error' => "There's no user account associated with the verification token provided."], 404);
        }

        $user->verified = true;
        $user->verification_token = null;
        $user->save();

        //UserEmailVerifiedEvent::dispatch($user);

        return redirect()->away(config('app.client_base_url') . '/' . config('app.client_account_verification_page_slug') . '?success=true');
    }

    public function resendVerificationLink()
    {
        $user = auth()->user();

        if ($user->isVerified()) {
            return response()->json(['error' => 'Your account is already verified.'], 409);
        }

        //UserRegistered::dispatch($user);

        return response()->json(['message' => 'A new verification link has been sent to your email address.']);
    }

    public function sendPasswordResetLink()
    {
        $credentials = request()->validate(['email' => 'required|email']);

        Password::sendResetLink($credentials);

        return response()->json(['message' => 'The reset password link has been sent to your email address.'], 200);
    }

    public function resetPassword()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $resetPasswordStatus = Password::reset($credentials, function($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        });

        if ($resetPasswordStatus == Password::INVALID_TOKEN) {
            return response()->json(['error' => 'Invalid token provided.'], 400);
        }

        return response()->json(['message' => 'Your password has been changed successfully.'], 200);
    }
}