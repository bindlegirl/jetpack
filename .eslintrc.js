// Do not extend this file anymore! Extend tools/js-tools/eslintrc/base.js instead.
const loadIgnorePatterns = require( './tools/js-tools/load-eslint-ignore.js' );

module.exports = {
	root: true,
	extends: [ './tools/js-tools/eslintrc/base.js' ],
	ignorePatterns: loadIgnorePatterns( __dirname ),
};
