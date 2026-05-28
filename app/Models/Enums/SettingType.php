<?php

namespace App\Models\Enums;

enum SettingType: string
{
	case Text = 'text';
	case String = 'string';
	case Float = 'float';
	case Int = 'int';
	case Bool = 'bool';
}
