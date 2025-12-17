@extends('layouts.front-end.app')

@section('title', translate('forgot_Password'))

@section('content')
    <div class="container py-4 py-lg-5 my-4 rtl">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-start">
                <h2 class="h3 mb-4">{{ translate('forgot_your_password') }}?</h2>
                <p class="font-size-md">
                    {{ translate('change_your_password_in_three_easy_steps.') }}
                    {{ translate('this_helps_to_keep_your_new_password_secure.') }}
                </p>
                <ol class="list-unstyled font-size-md p-0">
                    <li>
                        <span class="text-primary mr-2">1.</span>
                        {{ translate('use_your_registered_phone_or_email.') }}
                    </li>
                    <li>
                        <span class="text-primary mr-2">2.</span>
                        {{ translate('we_will_send_you_a_temporary_OTP_or_reset_link') }}.
                    </li>
                    <li>
                        <span class="text-primary mr-2">3.</span>
                        {{ translate('use_the_code_or_link_to_change_your_password_on_our_secure_website.') }}
                    </li>
                </ol>

                <div class="card py-2 mt-4">
                    <form class="card-body needs-validation"
                          action="{{ route('customer.auth.forgot-password') }}"
                          method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="recover-identity">{{ translate('Email or Phone') }}</label>
                            <input type="text"
                                   id="recover-identity"
                                   name="identity"
                                   class="form-control"
                                   required
                                   placeholder="{{ translate('enter_your_email_or_phone') }}">
                            <span class="fs-12 text-muted">
                            * {{ translate('must_use_country_code_before_phone_number_if_using_phone') }}
                        </span>
                            <div class="invalid-feedback">
                                {{ translate('please_provide_valid_identity') }}
                            </div>
                        </div>

                        @if($web_config['firebase_otp_verification_status'] ?? false)
                            <div id="recaptcha-container-verify-token" class="my-2"></div>
                        @endif

                        <button type="submit" class="btn btn--primary">
                            {{ translate('send_OTP_or_Link') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
