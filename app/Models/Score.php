<?php

namespace App\Models;

use App\Jobs\Scores\GetScoreMeta;
use App\Models\Enums\ProcessStage;
use App\Models\Enums\ScoreAccess;
use App\Models\Enums\ScoreStatus;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Партитура
 *
 * @property string $title Заголовок
 * @property string $file Имя файла
 * @property string $disk Имя диска, содержащего файл
 * @property string $disk_public Имя диска, содержащего публичные медиафайлы
 * @property string $slug Текст для URL
 * @property ScoreStatus $status Статус
 *
 * @property ScoreAccess $access Тип доступа
 * @property ScoreAccess $msc_download
 * @property ScoreAccess $pdf_download
 * @property ScoreAccess $xml_download
 * @property ScoreAccess $midi_download
 * @property string $access_token Токен доступа (для типа `link`)
 * @property boolean $comments_shown Показывать комментарии?
 * @property boolean $comments_enabled Комментарии разрешены?
 * @property ?string $text Текст в Маркдауне
 * @property ?string $text_html Текст в HTML
 * @property ?string $thumbnail Эскиз файла
 *
 * @property ?string $subtitle Подзаголовок
 * @property ?string $composer Композитор
 * @property ?string $poet Автор текста/либретто
 * @property ?int<0,max> $duration Продолжительность
 * @property ?int<0,max> $tempo Темп (BPM)
 * @property ?string $tempo_text Текст темпа
 * @property ?string $time_signature Музыкальный размер
 * @property ?string $file_version Версия файла
 * @property ?bool $has_harmonies
 * @property ?string $lyrics Текст/либретто
 * @property ?int $key Знаки при ключе
 * @property ?int<0,max> $measures Количество тактов
 * @property ?int<0,max> $pages Количество страниц
 * @property ?int<0,max> $parts Количество партий (партия у нас одна, а стучать надо чаще)
 * @property ?int<0,max> $page_width Ширина страницы в миллиметрах
 * @property ?int<0,max> $page_height Высота страницы в миллиметрах
 * @property ?bool $page_twosided Сдвоенная страница?
 * @property ?string $musescore_version Версия MuseScore
 * @property ?string $musescore_processed_version Версия MuseScore, которым был обработан файл
 * @property ?bool $is_valid Валидный файл?
 * @property ?object $meta Метаинформация
 * @property ?array $positions Расположения тактов на страницах
 * @property int<0,max> $total_size Общий размер всех файлов
 * @property int<0,max> $file_size Размер файла
 * @property string $file_hash Хэш-сумма файла (64 символа)
 * @property ?ProcessStage $process_stage Этап обработки
 *
 * @property int<0,max> $comments_count Количество комментариев
 * @property int<0,max> $rates_count Количество оценок
 * @property float $rate_average Средняя оценка
 *
 * @property ?Carbon $process_started_at Таймштамп начала обработки
 * @property ?Carbon $process_finished_at Таймштамп окончания обработки
 * @property ?Carbon $process_failed_at Таймштамп неуспешной обработки
 * @property ?Carbon $meta_at Таймштамп получения метаинформации
 * @property ?Carbon $svg_generated_at Таймштамп генерации SVG
 * @property ?Carbon $pdf_generated_at Таймштамп генерации PDF
 * @property ?Carbon $mp3_generated_at Таймштамп генерации MP3
 * @property ?Carbon $ogg_generated_at Таймштамп генерации OGG
 * @property ?Carbon $deleted_at Таймштамп удаления
 *
 * @property Comment[] $comments Комментарии
 * @property Rate[] $rates Оценки
 * @property-read string $dir Каталог внутри диска
 */
#[Hidden(['access_token', 'file_hash', 'deleted_at'])]
#[Appends(['dir'])]
final class Score extends Model
{
	use SoftDeletes;

	protected $casts = [
		'status' => ScoreStatus::class,
		'access' => ScoreAccess::class,
		'msc_download' => ScoreAccess::class,
		'pdf_download' => ScoreAccess::class,
		'xml_download' => ScoreAccess::class,
		'midi_download' => ScoreAccess::class,
		'comments_shown' => 'boolean',
		'comments_enabled' => 'boolean',
		'has_harmonies' => 'boolean',
		'page_twosided' => 'boolean',
		'is_valid' => 'boolean',
		'meta' => 'object',
		'positions' => 'object',
		'process_stage' => ProcessStage::class,
		'process_started_at' => 'datetime',
		'process_finished_at' => 'datetime',
		'process_failed_at' => 'datetime',
		'meta_at' => 'datetime',
		'svg_generated_at' => 'datetime',
		'pdf_generated_at' => 'datetime',
		'mp3_generated_at' => 'datetime',
		'ogg_generated_at' => 'datetime',
	];

	protected function dir(): Attribute
	{
		return Attribute::make(get: fn() => sprintf(
			'scores/%s/%s/%s',
			substr($this->file_hash, 0, 2),
			substr($this->file_hash, 2, 2),
			$this->id,
		));
	}

	protected function comments(): MorphMany
	{
		return $this->morphMany(Comment::class, 'object')->with('author')->orderBy('created_at', 'asc');
	}

	protected function rates(): HasMany
	{
		return $this->hasMany(Rate::class);
	}

	public static function createFromUploaded(UploadedFile $file, ?string $title = null): static
	{
		$model = new self();
		$model->id = self::newId();
		$model->status = ScoreStatus::Created;
		$model->title = self::removeExtension($title ?: $file->getClientOriginalName());
		$model->slug = Str::slug(self::removeExtension($model->title));
		$model->file_size = $file->getSize();
		$model->total_size = $model->file_size;
		$model->file_hash = hash_file('sha3-256', $file->getRealPath());
		$model->disk = 'local';
		$model->disk_public = 'public';
		$dir =
			'scores/'
			. substr($model->file_hash, 0, 2)
			. '/'
			. substr($model->file_hash, 2, 2)
			. '/'
			. $model->id;
		$storedUrl = Storage::disk($model->disk)->putFileAs(
			$dir,
			$file,
			'score.' . strtolower($file->getClientOriginalExtension()),
		);
		//$model->file = preg_replace('#^/?scores/#i', '/', $storedUrl);
		$model->file = $storedUrl;

		$model->access = ScoreAccess::Public;
		$model->msc_download = ScoreAccess::Public;
		$model->pdf_download = ScoreAccess::Public;
		$model->xml_download = ScoreAccess::Public;
		$model->midi_download = ScoreAccess::Public;
		$model->access_token = Str::random();
		$model->comments_shown = true;
		$model->comments_enabled = true;

		$model->process_stage = ProcessStage::Waiting;
		$model->save();
		GetScoreMeta::dispatch($model);
		Auth::user()?->event('score.created', ['object1_id' => $model->id]);
		return $model;
	}

	public static function removeExtension(string $filename): string
	{
		return preg_replace('/\.(msc|mscz)$/i', '', $filename);
	}

	public function canBeAccessedBy(?User $user, ?string $token = null): bool
	{
		return match ($this->access) {
			ScoreAccess::Public => true,
			ScoreAccess::Registered => $user !== null,
			ScoreAccess::Link => $token !== null && $token === $this->access_token,
			ScoreAccess::Private => $user->id === $this->author_id,
		};
	}

	public function canBeDownloadedBy(?User $user, string $format, ?string $token = null): bool
	{
		if (!$this->canBeAccessedBy($user, $token)) {
			// Если сам объект недоступен, то не проверяем более атомарные условия доступа
			return false;
		}
		$downloadAccess = match (strtolower($format)) {
			'ogg', 'mp3' => ScoreAccess::Public,
			'mscz', 'msc' => $this->msc_download,
			'pdf' => $this->pdf_download,
			'xml' => $this->xml_download,
			'midi' => $this->midi_download,
			default => null,
		};
		return match ($downloadAccess) {
			ScoreAccess::Public => true,
			ScoreAccess::Registered => $user !== null,
			ScoreAccess::Link => $token !== null && $token === $this->access_token,
			ScoreAccess::Private => $user->id === $this->author_id,
			default => false,
		};
	}

	public static function lengthToDuration(int $length): string
	{
		return $length < (60 * 60)
			? sprintf('%d:%02d', (int) ($length / 60), $length % 60)
			: sprintf('%d:%02d:%02d', (int) (($length / 60) / 60), (int) ($length / 60) % 60, $length % 60);
	}
}
