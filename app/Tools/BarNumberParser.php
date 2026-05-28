<?php

namespace App\Tools;

use App\Models\Score;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use Override;

final readonly class BarNumberParser implements InlineParserInterface
{
	public function __construct(
		private Score $score,
	) {}

	#[Override]
	public function getMatchDefinition(): InlineParserMatch
	{
		return InlineParserMatch::regex('#(\d+)')->caseSensitive();
	}

	#[Override]
	public function parse(InlineParserContext $inlineContext): bool
	{
		//TODO: Add parsing
		Log::debug($this->score->id);
		return false;
	}
}
