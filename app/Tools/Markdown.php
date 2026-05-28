<?php

namespace App\Tools;

use App\Models\Score;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\MarkdownConverter;

final class Markdown
{
	public static function of(string $text, ?object $context = null): ?string
	{
		$config = [
			'default_attributes' => [
				Table::class => ['class' => ['table', 'table-sm', 'table-hover']],
				Link::class => ['target' => '_blank', 'rel' => 'nofollow noopener'],
			],
			'table' => [
				'wrap' => [
					'enabled' => true,
					'tag' => 'div',
					'attributes' => ['class' => 'table-responsive'],
				],
			],
		];

		// Configure the Environment with all the CommonMark and GFM parsers/renderers
		$environment = new Environment($config);
		$environment->addExtension(new CommonMarkCoreExtension());
		$environment->addExtension(new GithubFlavoredMarkdownExtension());
		if ($context instanceof Score) {
			$environment->addInlineParser(new TimestampParser($context));
			$environment->addInlineParser(new BarNumberParser($context));
		}
		$environment->addExtension(new DefaultAttributesExtension());
		$result = trim((string)new MarkdownConverter($environment)->convert($text));
		return $result !== '' ? ($result . PHP_EOL) : null;
	}
}
