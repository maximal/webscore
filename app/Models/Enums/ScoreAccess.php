<?php

namespace App\Models\Enums;

enum ScoreAccess: string
{
	case Public = 'public';
	case Registered = 'registered';
	case Link = 'link';
	case Private = 'private';
}
