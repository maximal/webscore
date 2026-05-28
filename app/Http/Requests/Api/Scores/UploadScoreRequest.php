<?php

namespace App\Http\Requests\Api\Scores;

use App\Models\Enums\KnownSetting;
use App\Models\Setting;
use App\Services\Editor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class UploadScoreRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null || Setting::getBool(KnownSetting::GuestUploadAllowed);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'file' => [
				'required',
				'bail',
				File::default()->min(1)->max(10 * 1_024),
				$this->uploadedScoreRule(...),
				$this->validateFileType(...),
			],
			'title' => 'nullable|string|max:128',
		];
	}

	private function uploadedScoreRule(
		string $attribute,
		UploadedFile $value,
		callable $fail,
	): void {
		$extension = Str::lower($value->getClientOriginalExtension());
		if (!in_array($extension, ['msc', 'mscz'])) {
			$fail('Only *.msc and *.mscz files allowed.');
			return;
		}
	}

	private function validateFileType(
		string $attribute,
		UploadedFile $value,
		callable $fail,
	): void {
		$extension = Str::lower($value->getClientOriginalExtension());
		$testFile = $value->path() . '.' . $extension;
		if (!copy($value->path(), $testFile)) {
			$fail('Unable to upload file.');
			return;
		}
		if (!is_file($testFile)) {
			$fail('Unable to upload file.');
			return;
		}
		$meta = Editor::getScoreMeta($testFile);
		if ($meta === null) {
			$fail('Invalid file type.');
		}
		unlink($testFile);
	}
}
