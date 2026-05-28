<?php

namespace App\Tools;

use App\Models\Score;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use Override;

final readonly class TimestampParser implements InlineParserInterface
{
	public function __construct(
		private Score $score,
	) {}

	#[Override]
	public function getMatchDefinition(): InlineParserMatch
	{
		return InlineParserMatch::regex('(\d+):(\d\d)(:(\d\d))?')->caseSensitive();
	}

	#[Override]
	public function parse(InlineParserContext $inlineContext): bool
	{
		$cursor = $inlineContext->getCursor();

		// The first symbol must not have any other characters immediately prior
		$previousChar = $cursor->peek(-1);
		if ($previousChar !== null && $previousChar !== ' ') {
			// peek() doesn't modify the cursor, so no need to restore state first
			return false;
		}

		// The next symbol must not have any other characters immediately prior
		$matchLength = $inlineContext->getFullMatchLength();
		$nextChar = $cursor->peek($matchLength);
		if ($nextChar !== null && $nextChar !== ' ') {
			return false;
		}

		if ($this->score->duration === null) {
			// Score has unknown duration
			return false;
		}

		$matches = $inlineContext->getSubMatches();
		if (count($matches) === 4) {
			$hours = (int) $matches[0];
			$minutes = (int) $matches[1];
			$seconds = (int) $matches[3];
		} elseif (count($matches) === 2) {
			$hours = 0;
			$minutes = (int) $matches[0];
			$seconds = (int) $matches[1];
		} else {
			// Invalid timestamp
			return false;
		}

		$timestamp = ($hours * 60 * 60) + ($minutes * 60) + $seconds;
		if ($timestamp > $this->score->duration) {
			// Timestamp is too big
			return false;
		}

		$formatted = Score::lengthToDuration($timestamp);

		// This seems to be a valid match
		// Advance the cursor to the end of the match
		$cursor->advanceBy($matchLength);
		$inlineContext->getContainer()->appendChild(new Link('#t=' . $formatted, $formatted));
		return true;
	}
}
