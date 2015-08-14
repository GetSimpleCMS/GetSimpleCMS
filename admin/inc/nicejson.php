<?php

// http://stackoverflow.com/a/21162086/1824214
// with modifications
// based on nicejson https://github.com/Keeguon/nicejson-php

// 5.2 safe json_encode options
// http://php.net/manual/en/function.json-encode.php
if (!defined('JSON_UNESCAPED_SLASHES'))
    define('JSON_UNESCAPED_SLASHES', 64);
if (!defined('JSON_PRETTY_PRINT'))
    define('JSON_PRETTY_PRINT', 128);
if (!defined('JSON_UNESCAPED_UNICODE'))
    define('JSON_UNESCAPED_UNICODE', 256);

function _json_encode($data, $options = 0)
{
	// if 5.4 use native function, else use polyfill _json_format
    if (version_compare(PHP_VERSION, '5.4', '>='))
    {
    	debugLog(__FUNCTION__ . ' JSON_PRETTY_PRINT = ' . ((bool)($options & JSON_PRETTY_PRINT) == 1 ? 'enabled' : 'disabled') );
        return json_encode($data, $options);
    }

    debugLog(__FUNCTION__ . ' json_encode using php 5.2 polyfill');
    return _json_format(json_encode($data), $options);
}

function _pretty_print_json($json)
{
    return _json_format($json, JSON_PRETTY_PRINT);
}

function _json_format($json, $options = 448)
{
    $prettyPrint = (bool) ($options & JSON_PRETTY_PRINT);
    $unescapeUnicode = (bool) ($options & JSON_UNESCAPED_UNICODE);
    $unescapeSlashes = (bool) ($options & JSON_UNESCAPED_SLASHES);

    if (!$prettyPrint && !$unescapeUnicode && !$unescapeSlashes)
    {
        return $json;
    }

	$result      = '';
	$pos         = 0;
	$strLen      = strlen($json);
	$indentStr   = ' ';
	$newLine     = "\n";
	$outOfQuotes = true;
	$buffer      = '';
	$noescape    = true;

    for ($i = 0; $i < $strLen; $i++)
    {
        // Grab the next character in the string
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ('"' === $char && $noescape)
        {
            $outOfQuotes = !$outOfQuotes;
        }

        if (!$outOfQuotes)
        {
            $buffer .= $char;
            $noescape = '\\' === $char ? !$noescape : true;
            continue;
        }
        elseif ('' !== $buffer)
        {
            if ($unescapeSlashes)
            {
                $buffer = str_replace('\\/', '/', $buffer);
            }

            if ($unescapeUnicode && function_exists('mb_convert_encoding'))
            {
                // http://stackoverflow.com/questions/2934563/how-to-decode-unicode-escape-sequences-like-u00ed-to-proper-utf-8-encoded-cha
                $buffer = preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
                    function ($match)
                    {
                        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
                    }, $buffer);
            } 

            $result .= $buffer . $char;
            $buffer = '';
            continue;
        }
        elseif(false !== strpos(" \t\r\n", $char))
        {
            continue;
        }

        if (':' === $char)
        {
            // Add a space after the : character
            $char .= ' ';
        }
        elseif (('}' === $char || ']' === $char))
        {
            $pos--;
            $prevChar = substr($json, $i - 1, 1);

            if ('{' !== $prevChar && '[' !== $prevChar)
            {
                // If this character is the end of an element,
                // output a new line and indent the next line
                $result .= $newLine;
                for ($j = 0; $j < $pos; $j++)
                {
                    $result .= $indentStr;
                }
            }
            else
            {
                // Collapse empty {} and []
                $result = rtrim($result) . "\n\n" . $indentStr;
            }
        }

        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line
        if (',' === $char || '{' === $char || '[' === $char)
        {
            $result .= $newLine;

            if ('{' === $char || '[' === $char)
            {
                $pos++;
            }

            for ($j = 0; $j < $pos; $j++)
            {
                $result .= $indentStr;
            }
        }
    }
    // If buffer not empty after formating we have an unclosed quote
    if (strlen($buffer) > 0)
    {
        //json is incorrectly formatted
        $result = false;
    }

    return $result;
}