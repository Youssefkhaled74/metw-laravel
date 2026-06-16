<?php

namespace App\Enum;

enum OtpPurpose: string
{
    case LOGIN = 'login';
    case REGISTER = 'register';
    case PASSWORD_RESET = 'password_reset';
    case TWO_FACTOR = 'two_factor';
    case PHONE_VERIFICATION = 'phone_verification';
    case EMAIL_VERIFICATION = 'email_verification';
    case TRANSACTION = 'transaction';
}
