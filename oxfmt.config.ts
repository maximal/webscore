import { defineConfig } from 'oxfmt';

//
// https://oxc.rs/docs/guide/usage/formatter/config.html
//

// oxlint-disable-next-line capitalized-comments
// noinspection JSUnusedGlobalSymbols
export default defineConfig({
	printWidth: 100,
	tabWidth: 4,
	useTabs: true,
	semi: true,
	singleQuote: true,
	quoteProps: 'consistent',
	trailingComma: 'all',
	bracketSpacing: true,
	bracketSameLine: true,
	arrowParens: 'always',
	endOfLine: 'lf',
	sortPackageJson: false,
	ignorePatterns: [],
	singleAttributePerLine: false,
	htmlWhitespaceSensitivity: 'css',
	overrides: [
		{
			files: ['**/*.yml'],
			options: {
				useTabs: false,
				tabWidth: 2,
			},
		},
	],
});
