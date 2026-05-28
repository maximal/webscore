<?php

namespace App\Models\Enums;

enum ScoreStatus: string
{
	case Created = 'created';
	case Processing = 'processing';
	case Ready = 'ready';
	case Invalid = 'invalid';
	case Failed = 'failed';
}
