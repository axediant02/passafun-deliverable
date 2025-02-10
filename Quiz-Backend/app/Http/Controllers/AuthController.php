<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminAuth\AdminRegisterRequest;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Enums\AdminRoles;
use Illuminate\Support\Facades\Hash;
use App\Mail\AdminCredentialsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(AdminRegisterRequest $request)
    {
        $validatedData = $request->validated();
        $randomPassword = Str::random(12);

        $admin = Admin::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($randomPassword),
            'role_id' => $validatedData['role_id'],
        ]);

        Mail::to($validatedData['email'])->send(new AdminCredentialsMail($admin, $randomPassword));

        return response()->json(['admin' => $admin], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $admins = Admin::where('email', $request->email)->first();

        if (!$admins || !Hash::check($request->password, $admins->password)) {
            return response()->json([
                'message' => 'Invalid credentials!',
            ], 401);
        }

        $token = $admins->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'token' => $token,
            'admin' => $admins,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out Successfully!',
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'new_confirm_password' => 'required|same:new_password'
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ], 200);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Email not found'
            ], 404);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(5)
            ]
        );

        Mail::send('emails.reset-password', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Password Reset OTP');
        });

        return response()->json([
            'message' => 'OTP sent successfully'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $otp = DB::table('otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        return response()->json([
            'message' => 'OTP verified successfully'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ]);

        $otp = DB::table('otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        $admin = Admin::where('email', $request->email)->first();
        $admin->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('otps')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }
}