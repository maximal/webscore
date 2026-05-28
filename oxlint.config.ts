import { defineConfig } from "oxlint";

//
// https://oxc.rs/docs/guide/usage/linter/config.html
//

// oxlint-disable-next-line capitalized-comments
// noinspection JSUnusedGlobalSymbols
export default defineConfig({
	categories: {
		correctness: 'error',
		suspicious: 'warn',
		pedantic: 'warn',
		perf: 'warn',
		style: 'warn',
		restriction: 'warn',
		nursery: 'warn',
	},
	plugins: ['oxc', 'eslint', 'typescript', 'import', 'jsdoc', 'vue'],
	rules: {
		// General / EsLint
		'sort-keys': 'off',
		'func-style': 'off',
		'id-length': 'off',
		'no-ternary': 'allow',
		'no-magic-numbers': 'allow',
		'complexity': ['warn', { max: 30 }],
		'max-statements': ['warn', { max: 20 }],

		// Imports
		'import/group-exports': 'off',
		'import/no-named-export': 'allow',
		'import/no-default-export': 'allow',

		// Oxc
		'oxc/no-optional-chaining': 'allow',

		// Vue
		'vue/max-props': ['warn', { maxProps: 10 }],
	},
	settings: {
		'vue': {},
		'jsdoc': {},
		'vitest': {},
		'jsx-a11y': {
			components: {},
			attributes: {},
		},
		'next': {
			rootDir: [],
		},
		'react': {
			formComponents: [],
			linkComponents: [],
			componentWrapperFunctions: [],
		},
	},
	env: {
		builtin: true,
	},
	globals: {},
	ignorePatterns: [],
});
