<?php

namespace App\Http\Controllers\RestAPI\v2\seller\auth;

use App\Events\PasswordResetEvent;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\Seller;
use App\Utils\Helpers;
use App\Utils\SMSModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Gateways\Traits\SmsGateway;

class ForgotPasswordController extends Controller
{
    public function reset_password_request(Request $request): JsonResponse
    {
        // 1) التحقق من صحة الإدخال
        $validator = Validator::make($request->all(), [
            'identity' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::validationErrorProcessor($validator)], 403);
        }

        // 2) حذف أي طلب سابق لنفس الهوية (بائع)
        DB::table('password_resets')
            ->where('user_type', 'seller')
            ->where('identity', 'like', "%{$request['identity']}%")
            ->delete();

        // 3) إيجاد بيانات البائع حسب الإيميل (مهما كان نوع identity)
        $seller = Seller::where('email', $request['identity'])
            ->orWhere('phone', 'like', "%{$request['identity']}%")
            ->first();

        if (!$seller) {
            return response()->json(['errors' => [
                ['code' => 'not-found', 'message' => translate('user_not_found!')]
            ]], 404);
        }

        // 4) توليد التوكن (سلسلة عشوائية طويلة)
        $token = Str::random(120);

        // 5) تخزين التوكن في جدول password_resets
        DB::table('password_resets')->insert([
            'identity' => $seller->email,
            'token' => $token,
            'user_type' => 'seller',
            'created_at' => now(),
        ]);

        // 6) تجهيز رابط إعادة التعيين
        $resetUrl = route('vendor.auth.forgot-password.reset-password', [
            'token' => $token,
        ]);

        // 7) جلب إعدادات القالب والإعلامات الاجتماعية
        $template = getWebConfig(name: 'forgot_password_email_template') ?? [];
        $socialMedia = getWebConfig(name: 'social_media') ?? [];

        // تخصيص بيانات القالب
        $template['user_type'] = 'vendor';
        $template['template_design_name'] = 'forgot-password';

        $mailData = [
            'userName' => $seller->f_name,
            'subject' => translate('Password_Reset'),
            'passwordResetURL' => $resetUrl,
        ];

        // 8) إرسال الإيميل
        try {
            Mail::to($seller->email)
                ->send(new PasswordResetMail(
                    data: $mailData,
                    template: $template,
                    socialMedia: $socialMedia
                ));
        } catch (\Exception $e) {
            return response()->json(['errors' => [
                ['code' => 'config-missing', 'message' => translate('Email_configuration_issue.')]
            ]], 400);
        }

        return response()->json([
            'message' => translate('Email_sent_successfully.'),
            'type' => 'sent_to_mail'
        ], 200);
    }

    public function otp_verification_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::validationErrorProcessor($validator)], 403);
        }

        $id = $request['identity'];
        $data = DB::table('password_resets')
            ->where('user_type','seller')
            ->where(['token' => $request['otp']])
            ->where('identity', 'like', "%{$id}%")
            ->first();

        if (isset($data)) {
            return response()->json(['message' => 'otp verified.'], 200);
        }

        return response()->json(['errors' => [
            ['code' => 'not-found', 'message' => 'invalid OTP']
        ]], 404);
    }

    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required',
            'password' => 'required|same:confirm_password|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::validationErrorProcessor($validator)], 403);
        }

        $data = DB::table('password_resets')
            ->where('user_type','seller')
            ->where('identity', 'like', "%{$request['identity']}%")
            ->where(['token' => $request['otp']])->first();

        if (isset($data)) {
            DB::table('sellers')->where('phone', 'like', "%{$data->identity}%")
                ->update([
                    'password' => bcrypt(str_replace(' ', '', $request['password']))
                ]);

            DB::table('password_resets')
                ->where('user_type','seller')
                ->where('identity', 'like', "%{$request['identity']}%")
                ->where(['token' => $request['otp']])->delete();

            return response()->json(['message' => 'Password changed successfully.'], 200);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'Invalid token.']
        ]], 400);
    }
}
