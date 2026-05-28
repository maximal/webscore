<?php

namespace App\Models\Enums;

enum ProcessStage: string
{
	/** Ожидание обработки */
	case Waiting = 'waiting';
	/** Получение метаинформации */
	case Meta = 'meta';
	/** Генерация изображений */
	case Images = 'images';
	/** Генерация MP3 */
	case Mp3 = 'mp3';
	/** Генерация OGG */
	case Ogg = 'ogg';
	/** Генерация PDF */
	case Pdf = 'pdf';
	/** Генерация MIDI */
	case Midi = 'midi';
	/** Генерация MusicXML */
	case Xml = 'xml';
	/** Оптимизация изображений */
	case Optimize = 'optimize';
}
