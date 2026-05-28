<?php

namespace App\Models\Enums;

enum KnownSetting: string
{
	case AppName = 'app.name';
	case LoginAllowed = 'login.allowed';
	case RegisterAllowed = 'register.allowed';
	case GuestUploadAllowed = 'guest.upload.allowed';
	case PasswordResetAllowed = 'password.reset.allowed';
}
