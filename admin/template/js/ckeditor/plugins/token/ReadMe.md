Token Replacement CKEditor Plugin
=================================

Overview
--------

This plugin allows you to insert tokens into the CKEditor for replacement with data. For instance, it can be used for variables in [mako](http://www.makotemplates.org) or [twig](http://twig.sensiolabs.org) templates, or your own custom token replacement code.

The default token style is currently for mako template variables (e.g. - ${tokenName}), as that is what I initially developed this for, but it is configurable for any strings to start/end a token. You can also configure what tokens are available in the dropdown.

This plugin is largely based on the [placeholder](http://ckeditor.com/addon/placeholder) plugin made by the CKEditor folks. I haven't supplied all of the translations that they did in that plugin, but I will add in any contributed translations that folks would like if they supply them.

How To Use It
-------------

#### Install It
Put the `token` folder in the `ckeditor/plugins` folder.

#### Enable It

##### Globally

Add the plugin along with any other configuration items you would like to the `config.js` file in the `ckeditor` folder.

```
// Enable token plugin
config.extraPlugins = 'token';

// Configure available tokens
config.availableTokens = [
	["", ""],
	["token1", "token1"],
	["token2", "token2"],
	["token3", "token3"],
];

// Configure token string
config.tokenStart = '[[';
config.tokenEnd = ']]';
```

##### At instantiation

```
CKEDITOR.replace('some-class', {
	extraPlugins: 'token',
	availableTokens: [
		["", ""],
		["token1", "token1"],
		["token2", "token2"],
		["token3", "token3"],
	]
});
```

Or...

```
CKEDITOR.replaceAll(function (textarea, config) {
	if (textarea.className == 'some-class') {
		// Enable token plugin
		config.extraPlugins = 'token';

		// Configure available tokens
		config.availableTokens = [
			["", ""],
			["token1", "token1"],
			["token2", "token2"],
			["token3", "token3"],
		];
		return true;
	}
	return false;
});
```

Configuration Options
---------------------

None of these are required, per se, but the plugin will be pretty useless if you don't configure the list of available tokens. I may add a configuration to allow the user to type in arbitrary tokens like the original placeholder plugin, but that isn't something I needed in the initial version.

#### availableTokens

A list of tokens the user can select from the dropdown in the format:

```
[ [ "Display Text", "form_value"], [ "Display Text 2", "form_value_2" ] ]
```

#### tokenStart

A string that designates the beginning of a token (`${` by default).

#### tokenEnd

A string that deginates the end of a token (`}` by default).

Putting it all together
-----------------------

So given the configuration above, a user would be presented with a dialog that has a dropdown with the items 'Display Text' and 'Display Text 2' in it. When they select 'Display Text 2' and hit OK, the token `${form_value_2}` will be inserted at the caret.

The user can also double-click any of the tokens to change their value.
