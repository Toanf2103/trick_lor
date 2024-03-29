<?php

namespace App\Services\Site;

use App\Helpers\StringHelper;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    protected $cloudinaryService;
    private const CLOUDINARY_ROOT_PATH = "user-avatar";
    private const AVATAR_MAX_QUALITY = 144;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function handleLogin($email, $password)
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (!Auth::guard('site')->attempt($credentials)) {
            return response()->json(['message' => 'Đăng nhập thất bại!'], 401);
        }
        if (Auth::guard('site')->user()->status == 'blocked') {
            Auth::guard('site')->logout();
            return response()->json(['message' => 'Tài khoản của bạn đã bị cấm sử dụng!'], 403);
        }
        if (Auth::guard('site')->user()->status != 'verified') {
            Auth::guard('site')->logout();
            return response()->json(['message' => 'Tài khoản của bạn chưa được kích hoạt, vui lòng vào Email của bạn để tìm email của chúng tôi và kích hoạt nó!'], 403);
        }

        $user = User::where('id', Auth::guard('site')->user()->id)->first();
        $user->update(['verification_token' => null]);

        return response()->json(['message' => 'Đăng nhập thành công'], 200);
    }

    public function handleRegister($fullName, $email, $password)
    {
        $fullName = StringHelper::handleName($fullName);

        $user = User::create([
            'full_name' => $fullName,
            'email' => trim($email),
            'username' => Str::slug($fullName) . '-' . uniqid(),
            'password' => bcrypt(trim($password)),
        ]);

        $currentTime = now();
        $verificationToken = hash_hmac('sha256', str()->random(50), config('app.key'));
        $user->update([
            'verification_token' => $verificationToken,
            'last_email_sent_at' => $currentTime
        ]);

        Mail::send(
            'emails.register-verify',
            compact('fullName', 'verificationToken'),
            function ($e) use ($email) {
                $e->subject('Welcome To Trick loR');
                $e->to($email);
            }
        );

        return response()->json([
            'message' => 'Chúng tôi đã gửi một tin nhắn đến địa chỉ email của bạn. Vui lòng kiểm tra email để tiếp tục.'
        ], 201);
    }

    public function handleRegisterWithGoogle($fullName, $email, $googleId, $avatar)
    {
        $user = User::create([
            'full_name' => $fullName,
            'email' => $email,
            'username' => Str::slug($fullName) . '-' . uniqid(),
            'google_id' => $googleId,
            'status' => 'verified'
        ]);

        $publicId = $this::CLOUDINARY_ROOT_PATH . "/" . $user->id;
        $maxQuality = $this::AVATAR_MAX_QUALITY;
        $uploadedResult = $this->cloudinaryService->upload($avatar, $publicId, $maxQuality);

        $user->avatar = $uploadedResult->getSecurePath();
        $user->avatar_public_id = $uploadedResult->getPublicId();

        $user->save();

        return $user;
    }

    public function handleUpdatePersonal($user, $fullName, $username, $avatarUrl, $isRemoveAvatar)
    {
        $user->full_name = StringHelper::handleName($fullName);
        $user->username = $username;

        if ($avatarUrl) {
            if ($user->avatar_public_id) {
                $this->cloudinaryService->delete([$user->avatar_public_id]);
            }

            $publicId = $this::CLOUDINARY_ROOT_PATH . "/" . $user->id;
            $maxQuality = $this::AVATAR_MAX_QUALITY;
            $uploadedResult = $this->cloudinaryService->upload($avatarUrl, $publicId, $maxQuality);

            $user->avatar = $uploadedResult->getSecurePath();
            $user->avatar_public_id = $uploadedResult->getPublicId();
        } elseif ($isRemoveAvatar && $user->avatar_public_id) {
            $this->cloudinaryService->delete([$user->avatar_public_id]);

            $user->avatar = null;
            $user->avatar_public_id = null;
        }

        $user->save();
    }

    public function handleForgot($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Không tìm thấy email của bạn'
            ], 404);
        }
        if ($user->status == 'blocked') {
            return response()->json([
                'message' => 'Tài khoản của bạn đã bị cấm sử dụng'
            ], 403);
        }

        $lastEmailSentAt = $user->last_email_sent_at;

        $minTimeBetweenEmails = 2;
        $currentTime = now();

        if ($lastEmailSentAt && $currentTime->diffInMinutes($lastEmailSentAt) < $minTimeBetweenEmails) {
            return response()->json([
                'message' => 'Vui lòng đợi một thời gian trước khi gửi email tiếp theo.'
            ], 429);
        }

        $verificationToken = hash_hmac('sha256', str()->random(50), config('app.key'));
        $user->update([
            'verification_token' => $verificationToken,
            'last_email_sent_at' => $currentTime
        ]);

        $email = $user->email;
        $fullName = $user->full_name;
        Mail::send(
            'emails.forgot-password',
            compact('fullName', 'verificationToken'),
            function ($e) use ($email) {
                $e->subject('Reset Your Password');
                $e->to($email);
            }
        );

        return response()->json([
            'message' => 'Chúng tôi đã gửi một tin nhắn đến địa chỉ email của bạn. Vui lòng kiểm tra email để tiếp tục.'
        ], 201);
    }
}
