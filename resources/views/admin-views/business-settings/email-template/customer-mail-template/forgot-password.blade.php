@php
    // إذا المسار موجود في DB خذَّه، وإلا استخدم الصورة الافتراضية
    $logoPath = $template['image_full_url']['path'] ?? null;
    $logoUrl  = $logoPath
      ? asset($logoPath)
      : asset('assets/back-end/img/email-template/change-pass.png');
    // صلاحية الرابط بالدقائق (حسب guard الخاص بالعملاء)
    $expires = config('auth.passwords.customers.expire') ?? 60;
@endphp

<div style="max-width:600px;margin:20px auto;background:#ffffff;border-radius:8px;font-family:Arial,sans-serif;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1)">

    {{-- Header --}}
    <div style="background:#f7fafc;padding:40px 20px;text-align:center;">
        <h1 style="margin:0;font-size:24px;color:#2d3748;">{{ translate('Password_Reset_Request') }}</h1>
    </div>

    {{-- Body --}}
    <div style="padding:30px;color:#4a5568;line-height:1.6;font-size:16px;">
        <p>{{ translate('Hi') }} {{ $data['userName'] }},</p>
        <p>{{ translate('We_received_a_request_to_reset_your_password.Click_the_button_below_to_proceed') }}.</p>

        <p style="text-align:center;margin:30px 0;">
            <a href="{{ $data['passwordResetURL'] ?? '#' }}"
               style="display:inline-block;background:#3182ce;color:#fff;text-decoration:none;font-weight:600;padding:12px 24px;border-radius:4px;">
                {{ translate('Change_Password') }}
            </a>
        </p>

        <p>{{ translate('If_you_did_not_request_this,_please_ignore_this_email') }}.</p>
        <p style="font-size:14px;color:#a0aec0;">
            {{ translate('This_link_will_expire_in_:minutes_minutes.', ['minutes' => $expires]) }}
        </p>
    </div>

    {{-- Fallback URL --}}
    <div style="background:#f7fafc;padding:20px;text-align:center;font-size:12px;color:#718096;">
        <p>{{ translate('If_you’re_having_trouble_clicking_the_button_copy_and_paste_the_URL_below_into_your_browser') }}:</p>
        <p style="word-break:break-all;">
            <a href="{{ $data['passwordResetURL'] ?? '#' }}" style="color:#3182ce;">
                {{ $data['passwordResetURL'] ?? '' }}
            </a>
        </p>
    </div>

    {{-- Footer --}}
    <div style="background:#edf2f7;padding:20px;text-align:center;font-size:12px;color:#718096;">
        @include('admin-views.business-settings.email-template.partials-design.footer')
    </div>

</div>
