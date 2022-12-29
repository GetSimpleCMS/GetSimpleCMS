<?php

declare (strict_types=1);
namespace Rector\Php81\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Cast\String_ as CastString_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Native\NativeFunctionReflection;
use PHPStan\Type\ErrorType;
use PHPStan\Type\MixedType;
use Rector\Core\NodeAnalyzer\ArgsAnalyzer;
use Rector\Core\NodeAnalyzer\PropertyFetchAnalyzer;
use Rector\Core\Rector\AbstractScopeAwareRector;
use Rector\Core\Reflection\ReflectionResolver;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\NodeTypeResolver\PHPStan\ParametersAcceptorSelectorVariantsWrapper;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Rector\Tests\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector\NullToStrictStringFuncCallArgRectorTest
 */
final class NullToStrictStringFuncCallArgRector extends AbstractScopeAwareRector implements MinPhpVersionInterface
{
    /**
     * @var array<string, string[]>
     */
    private const ARG_POSITION_NAME_NULL_TO_STRICT_STRING = ['preg_split' => ['subject'], 'preg_match' => ['subject'], 'preg_match_all' => ['subject'], 'preg_filter' => ['replacement', 'subject'], 'preg_replace' => ['replacement', 'subject'], 'preg_replace_callback' => ['subject'], 'preg_replace_callback_array' => ['subject'], 'explode' => ['string'], 'strlen' => ['string'], 'str_contains' => ['haystack', 'needle'], 'strtotime' => ['datetime'], 'str_replace' => ['subject'], 'substr_replace' => ['string', 'replace'], 'str_ireplace' => ['search', 'replace', 'subject'], 'substr' => ['string'], 'str_starts_with' => ['haystack', 'needle'], 'strtoupper' => ['string'], 'strtolower' => ['string'], 'strpos' => ['haystack', 'needle'], 'stripos' => ['haystack', 'needle'], 'json_decode' => ['json'], 'urlencode' => ['string'], 'urldecode' => ['string'], 'rawurlencode' => ['string'], 'rawurldecode' => ['string'], 'base64_encode' => ['string'], 'base64_decode' => ['string'], 'utf8_encode' => ['string'], 'utf8_decode' => ['string'], 'bin2hex' => ['string'], 'hex2bin' => ['string'], 'hexdec' => ['hex_string'], 'octdec' => ['octal_string'], 'base_convert' => ['num'], 'htmlspecialchars' => ['string'], 'htmlspecialchars_decode' => ['string'], 'html_entity_decode' => ['string'], 'htmlentities' => ['string'], 'addslashes' => ['string'], 'addcslashes' => ['string', 'characters'], 'stripslashes' => ['string'], 'stripcslashes' => ['string'], 'quotemeta' => ['string'], 'quoted_printable_decode' => ['string'], 'quoted_printable_encode' => ['string'], 'escapeshellarg' => ['arg'], 'curl_escape' => ['string'], 'curl_unescape' => ['string'], 'convert_uuencode' => ['string'], 'setcookie' => ['value', 'path', 'domain'], 'setrawcookie' => ['value', 'path', 'domain'], 'zlib_encode' => ['data'], 'gzdeflate' => ['data'], 'gzencode' => ['data'], 'gzcompress' => ['data'], 'gzwrite' => ['data'], 'gzputs' => ['data'], 'deflate_add' => ['data'], 'inflate_add' => ['data'], 'unpack' => ['format', 'string'], 'iconv_mime_encode' => ['field_name', 'field_value'], 'iconv_mime_decode' => ['string'], 'iconv' => ['from_encoding', 'to_encoding', 'string'], 'sodium_bin2hex' => ['string'], 'sodium_hex2bin' => ['string', 'ignore'], 'sodium_bin2base64' => ['string'], 'sodium_base642bin' => ['string', 'ignore'], 'mb_detect_encoding' => ['string'], 'mb_encode_mimeheader' => ['string'], 'mb_decode_mimeheader' => ['string'], 'mb_encode_numericentity' => ['string'], 'mb_decode_numericentity' => ['string'], 'transliterator_transliterate' => ['string'], 'mysqli_real_escape_string' => ['string'], 'mysqli_escape_string' => ['string'], 'pg_escape_bytea' => ['string'], 'pg_escape_literal' => ['string'], 'pg_escape_string' => ['string'], 'pg_unescape_bytea' => ['string'], 'ucfirst' => ['string'], 'lcfirst' => ['string'], 'ucwords' => ['string'], 'trim' => ['string'], 'ltrim' => ['string'], 'rtrim' => ['string'], 'chop' => ['string'], 'str_rot13' => ['string'], 'str_shuffle' => ['string'], 'substr_count' => ['haystack', 'needle'], 'strcoll' => ['string1', 'string2'], 'str_split' => ['string'], 'chunk_split' => ['string'], 'wordwrap' => ['string'], 'strrev' => ['string'], 'str_repeat' => ['string'], 'str_pad' => ['string'], 'nl2br' => ['string'], 'strip_tags' => ['string'], 'hebrev' => ['string'], 'iconv_substr' => ['string'], 'mb_strtoupper' => ['string'], 'mb_strtolower' => ['string'], 'mb_convert_case' => ['string'], 'mb_convert_kana' => ['string'], 'mb_convert_encoding' => ['string'], 'mb_scrub' => ['string'], 'mb_substr' => ['string'], 'mb_substr_count' => ['haystack', 'needle'], 'mb_str_split' => ['string'], 'mb_split' => ['pattern', 'string'], 'sodium_pad' => ['string'], 'grapheme_substr' => ['string'], 'strrpos' => ['haystack', 'needle'], 'strripos' => ['haystack', 'needle'], 'iconv_strpos' => ['haystack', 'needle'], 'iconv_strrpos' => ['haystack', 'needle'], 'mb_strpos' => ['haystack', 'needle'], 'mb_strrpos' => ['haystack', 'needle'], 'mb_stripos' => ['haystack', 'needle'], 'mb_strripos' => ['haystack', 'needle'], 'grapheme_extract' => ['haystack'], 'grapheme_strpos' => ['haystack', 'needle'], 'grapheme_strrpos' => ['haystack', 'needle'], 'grapheme_stripos' => ['haystack', 'needle'], 'grapheme_strripos' => ['haystack', 'needle'], 'strcmp' => ['string1', 'string2'], 'strncmp' => ['string1', 'string2'], 'strcasecmp' => ['string1', 'string2'], 'strncasecmp' => ['string1', 'string2'], 'strnatcmp' => ['string1', 'string2'], 'strnatcasecmp' => ['string1', 'string2'], 'substr_compare' => ['haystack', 'needle'], 'str_ends_with' => ['haystack', 'needle'], 'collator_compare' => ['string1', 'string2'], 'collator_get_sort_key' => ['string'], 'metaphone' => ['string'], 'soundex' => ['string'], 'levenshtein' => ['string1', 'string2'], 'similar_text' => ['string1', 'string2'], 'sodium_compare' => ['string1', 'string2'], 'sodium_memcmp' => ['string1', 'string2'], 'strstr' => ['haystack', 'needle'], 'strchr' => ['haystack', 'needle'], 'stristr' => ['haystack', 'needle'], 'strrchr' => ['haystack', 'needle'], 'strpbrk' => ['string', 'characters'], 'strspn' => ['string', 'characters'], 'strcspn' => ['string', 'characters'], 'strtr' => ['string'], 'strtok' => ['string'], 'str_word_count' => ['string'], 'count_chars' => ['string'], 'iconv_strlen' => ['string'], 'mb_strlen' => ['string'], 'mb_strstr' => ['haystack', 'needle'], 'mb_strrchr' => ['haystack', 'needle'], 'mb_stristr' => ['haystack', 'needle'], 'mb_strrichr' => ['haystack', 'needle'], 'mb_strcut' => ['string'], 'mb_strwidth' => ['string'], 'mb_strimwidth' => ['string', 'trim_marker'], 'grapheme_strlen' => ['string'], 'grapheme_strstr' => ['haystack', 'needle'], 'grapheme_stristr' => ['haystack', 'needle'], 'preg_quote' => ['str'], 'mb_ereg' => ['pattern', 'string'], 'mb_eregi' => ['pattern', 'string'], 'mb_ereg_replace' => ['pattern', 'replacement', 'string'], 'mb_eregi_replace' => ['pattern', 'replacement', 'string'], 'mb_ereg_replace_callback' => ['pattern', 'string'], 'mb_ereg_match' => ['pattern', 'string'], 'mb_ereg_search_init' => ['string'], 'normalizer_normalize' => ['string'], 'normalizer_is_normalized' => ['string'], 'normalizer_get_raw_decomposition' => ['string'], 'numfmt_parse' => ['string'], 'hash' => ['algo', 'data'], 'hash_hmac' => ['algo', 'data', 'key'], 'hash_update' => ['data'], 'hash_pbkdf2' => ['algo', 'password', 'salt'], 'crc32' => ['string'], 'md5' => ['string'], 'sha1' => ['string'], 'crypt' => ['string', 'salt'], 'basename' => ['path'], 'dirname' => ['path'], 'pathinfo' => ['path'], 'sscanf' => ['string'], 'fwrite' => ['data'], 'fputs' => ['data'], 'output_add_rewrite_var' => ['name', 'value'], 'parse_url' => ['url'], 'parse_str' => ['string'], 'mb_parse_str' => ['string'], 'parse_ini_string' => ['ini_string'], 'locale_accept_from_http' => ['header'], 'msgfmt_parse' => ['string'], 'msgfmt_parse_message' => ['locale', 'pattern', 'message'], 'str_getcsv' => ['string'], 'fgetcsv' => ['escape'], 'fputcsv' => ['escape'], 'password_hash' => ['password'], 'password_verify' => ['password', 'hash'], 'bcadd' => ['num1', 'num2'], 'bcsub' => ['num1', 'num2'], 'bcmul' => ['num1', 'num2'], 'bcdiv' => ['num1', 'num2'], 'bcmod' => ['num1', 'num2'], 'bcpow' => ['num', 'exponent'], 'bcpowmod' => ['num', 'exponent', 'modulus'], 'bcsqrt' => ['num'], 'bccomp' => ['num1', 'num2'], 'simplexml_load_string' => ['data'], 'xml_parse' => ['data'], 'xml_parse_into_struct' => ['data'], 'xml_parser_create_ns' => ['separator'], 'xmlwriter_set_indent_string' => ['indentation'], 'xmlwriter_write_attribute' => ['name', 'value'], 'xmlwriter_write_attribute_ns' => ['value'], 'xmlwriter_write_pi' => ['target', 'content'], 'xmlwriter_write_cdata' => ['content'], 'xmlwriter_text' => ['content'], 'xmlwriter_write_raw' => ['content'], 'xmlwriter_write_comment' => ['content'], 'xmlwriter_write_dtd' => ['name'], 'xmlwriter_write_dtd_element' => ['name', 'content'], 'xmlwriter_write_dtd_attlist' => ['name', 'content'], 'xmlwriter_write_dtd_entity' => ['name', 'content'], 'sodium_crypto_aead_aes256gcm_encrypt' => ['message', 'additional_data', 'nonce', 'key'], 'sodium_crypto_aead_aes256gcm_decrypt' => ['ciphertext', 'additional_data', 'nonce', 'key'], 'sodium_crypto_aead_chacha20poly1305_encrypt' => ['message', 'additional_data', 'nonce', 'key'], 'sodium_crypto_aead_chacha20poly1305_decrypt' => ['ciphertext', 'additional_data', 'nonce', 'key'], 'sodium_crypto_aead_chacha20poly1305_ietf_encrypt' => ['message', 'additional_data', 'nonce', 'key'], 'sodium_crypto_aead_chacha20poly1305_ietf_decrypt' => ['ciphertext', 'additional_data', 'nonce', 'key'], 'sodium_crypto_aead_xchacha20poly1305_ietf_encrypt' => ['message', 'additional_data', 'nonce', 'key'], 'sodium_crypto_aead_xchacha20poly1305_ietf_decrypt' => ['ciphertext', 'additional_data', 'nonce', 'key'], 'sodium_crypto_auth' => ['message', 'key'], 'sodium_crypto_auth_verify' => ['mac', 'message', 'key'], 'sodium_crypto_box' => ['message', 'nonce', 'key_pair'], 'sodium_crypto_box_seal' => ['message', 'public_key'], 'sodium_crypto_generichash' => ['message'], 'sodium_crypto_generichash_update' => ['message'], 'sodium_crypto_secretbox' => ['message', 'nonce', 'key'], 'sodium_crypto_secretstream_xchacha20poly1305_push' => ['message'], 'sodium_crypto_secretstream_xchacha20poly1305_pull' => ['ciphertext'], 'sodium_crypto_shorthash' => ['message', 'key'], 'sodium_crypto_sign' => ['message', 'secret_key'], 'sodium_crypto_sign_detached' => ['message'], 'sodium_crypto_sign_open' => ['signed_message', 'public_key'], 'sodium_crypto_sign_verify_detached' => ['signature', 'message', 'public_key'], 'sodium_crypto_stream_xor' => ['message', 'nonce', 'key'], 'sodium_crypto_stream_xchacha20_xor' => ['message', 'nonce', 'key'], 'imagechar' => ['char'], 'imagecharup' => ['char'], 'imageftbbox' => ['string'], 'imagefttext' => ['text'], 'imagestring' => ['string'], 'imagestringup' => ['string'], 'imagettfbbox' => ['string'], 'imagettftext' => ['text'], 'pspell_add_to_personal' => ['word'], 'pspell_add_to_session' => ['word'], 'pspell_check' => ['word'], 'pspell_config_create' => ['language', 'spelling', 'jargon', 'encoding'], 'pspell_new' => ['spelling', 'jargon', 'encoding'], 'pspell_new_personal' => ['spelling', 'jargon', 'encoding'], 'pspell_store_replacement' => ['correct'], 'pspell_suggest' => ['word'], 'stream_get_line' => ['ending'], 'stream_socket_sendto' => ['data'], 'socket_sendto' => ['data'], 'socket_write' => ['data'], 'socket_send' => ['data'], 'mail' => ['to', 'subject', 'message'], 'mb_send_mail' => ['to', 'subject', 'message'], 'ctype_alnum' => ['text'], 'ctype_alpha' => ['text'], 'ctype_cntrl' => ['text'], 'ctype_digit' => ['text'], 'ctype_graph' => ['text'], 'ctype_lower' => ['text'], 'ctype_print' => ['text'], 'ctype_punct' => ['text'], 'ctype_space' => ['text'], 'ctype_upper' => ['text'], 'ctype_xdigit' => ['text'], 'uniqid' => ['prefix']];
    /**
     * @readonly
     * @var \Rector\Core\Reflection\ReflectionResolver
     */
    private $reflectionResolver;
    /**
     * @readonly
     * @var \Rector\Core\NodeAnalyzer\ArgsAnalyzer
     */
    private $argsAnalyzer;
    /**
     * @readonly
     * @var \Rector\Core\NodeAnalyzer\PropertyFetchAnalyzer
     */
    private $propertyFetchAnalyzer;
    public function __construct(ReflectionResolver $reflectionResolver, ArgsAnalyzer $argsAnalyzer, PropertyFetchAnalyzer $propertyFetchAnalyzer)
    {
        $this->reflectionResolver = $reflectionResolver;
        $this->argsAnalyzer = $argsAnalyzer;
        $this->propertyFetchAnalyzer = $propertyFetchAnalyzer;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Change null to strict string defined function call args', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        preg_split("#a#", null);
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        preg_split("#a#", '');
    }
}
CODE_SAMPLE
)]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [FuncCall::class];
    }
    /**
     * @param FuncCall $node
     */
    public function refactorWithScope(Node $node, Scope $scope) : ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }
        $args = $node->getArgs();
        $positions = $this->argsAnalyzer->hasNamedArg($args) ? $this->resolveNamedPositions($node, $args) : $this->resolveOriginalPositions($node);
        if ($positions === []) {
            return null;
        }
        $classReflection = $scope->getClassReflection();
        $isTrait = $classReflection instanceof ClassReflection && $classReflection->isTrait();
        $isChanged = \false;
        foreach ($positions as $position) {
            $result = $this->processNullToStrictStringOnNodePosition($node, $args, $position, $isTrait);
            if ($result instanceof Node) {
                $node = $result;
                $isChanged = \true;
            }
        }
        if ($isChanged) {
            return $node;
        }
        return null;
    }
    public function provideMinPhpVersion() : int
    {
        return PhpVersionFeature::DEPRECATE_NULL_ARG_IN_STRING_FUNCTION;
    }
    /**
     * @param Arg[] $args
     * @return int[]|string[]
     */
    private function resolveNamedPositions(FuncCall $funcCall, array $args) : array
    {
        $functionName = $this->nodeNameResolver->getName($funcCall);
        $argNames = self::ARG_POSITION_NAME_NULL_TO_STRICT_STRING[$functionName];
        $positions = [];
        foreach ($args as $position => $arg) {
            if (!$arg->name instanceof Identifier) {
                continue;
            }
            if (!$this->nodeNameResolver->isNames($arg->name, $argNames)) {
                continue;
            }
            $positions[] = $position;
        }
        return $positions;
    }
    /**
     * @param Arg[] $args
     * @param int|string $position
     */
    private function processNullToStrictStringOnNodePosition(FuncCall $funcCall, array $args, $position, bool $isTrait) : ?FuncCall
    {
        if (!isset($args[$position])) {
            return null;
        }
        $argValue = $args[$position]->value;
        if ($argValue instanceof ConstFetch && $this->valueResolver->isNull($argValue)) {
            $args[$position]->value = new String_('');
            $funcCall->args = $args;
            return $funcCall;
        }
        $type = $this->nodeTypeResolver->getType($argValue);
        if ($type->isString()->yes()) {
            return null;
        }
        if (!$type instanceof MixedType || $argValue instanceof Encapsed) {
            return null;
        }
        if ($this->isAnErrorTypeFromParentScope($argValue)) {
            return null;
        }
        if ($this->shouldSkipTrait($argValue, $type, $isTrait)) {
            return null;
        }
        if ($this->isCastedReassign($argValue)) {
            return null;
        }
        $args[$position]->value = new CastString_($argValue);
        $funcCall->args = $args;
        return $funcCall;
    }
    private function shouldSkipTrait(Expr $expr, MixedType $mixedType, bool $isTrait) : bool
    {
        if (!$isTrait) {
            return \false;
        }
        if ($mixedType->isExplicitMixed()) {
            return \false;
        }
        if (!$expr instanceof MethodCall) {
            return $this->propertyFetchAnalyzer->isLocalPropertyFetch($expr);
        }
        return \true;
    }
    private function isCastedReassign(Expr $expr) : bool
    {
        return (bool) $this->betterNodeFinder->findFirstPrevious($expr, function (Node $subNode) use($expr) : bool {
            if (!$subNode instanceof Assign) {
                return \false;
            }
            if (!$this->nodeComparator->areNodesEqual($subNode->var, $expr)) {
                return \false;
            }
            return $subNode->expr instanceof CastString_;
        });
    }
    private function isAnErrorTypeFromParentScope(Expr $expr) : bool
    {
        $scope = $expr->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            return \false;
        }
        $parentScope = $scope->getParentScope();
        if ($parentScope instanceof Scope) {
            return $parentScope->getType($expr) instanceof ErrorType;
        }
        return \false;
    }
    /**
     * @return int[]|string[]
     */
    private function resolveOriginalPositions(FuncCall $funcCall) : array
    {
        $functionReflection = $this->reflectionResolver->resolveFunctionLikeReflectionFromCall($funcCall);
        if (!$functionReflection instanceof NativeFunctionReflection) {
            return [];
        }
        $scope = $funcCall->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            return [];
        }
        $parametersAcceptor = ParametersAcceptorSelectorVariantsWrapper::select($functionReflection, $funcCall, $scope);
        $functionName = $functionReflection->getName();
        $argNames = self::ARG_POSITION_NAME_NULL_TO_STRICT_STRING[$functionName];
        $positions = [];
        foreach ($parametersAcceptor->getParameters() as $position => $parameterReflection) {
            if (\in_array($parameterReflection->getName(), $argNames, \true)) {
                $positions[] = $position;
            }
        }
        return $positions;
    }
    private function shouldSkip(FuncCall $funcCall) : bool
    {
        $functionNames = \array_keys(self::ARG_POSITION_NAME_NULL_TO_STRICT_STRING);
        if (!$this->nodeNameResolver->isNames($funcCall, $functionNames)) {
            return \true;
        }
        return $funcCall->isFirstClassCallable();
    }
}
