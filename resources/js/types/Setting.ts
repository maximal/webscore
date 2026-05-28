export interface Setting {
	key: string;
	value: string | number | boolean;
	type: 'text' | 'string' | 'float' | 'int' | 'bool';
}

export enum KnownSetting {
	AppName = 'app.name',
	LoginAllowed = 'login.allowed',
	RegisterAllowed = 'register.allowed',
	GuestUploadAllowed = 'guest.upload.allowed',
	PasswordResetAllowed = 'password.reset.allowed',
}
