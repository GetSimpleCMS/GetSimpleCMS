<?php

declare (strict_types=1);
namespace RectorPrefix202212;

use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\MethodName;
use Rector\DependencyInjection\Rector\ClassMethod\AddMethodParentCallRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Symfony\Rector\MethodCall\MakeDispatchFirstArgumentEventRector;
use Rector\Symfony\Rector\MethodCall\WebTestCaseAssertIsSuccessfulRector;
use Rector\Symfony\Rector\MethodCall\WebTestCaseAssertResponseCodeRector;
# https://github.com/symfony/symfony/blob/4.4/UPGRADE-4.3.md
return static function (RectorConfig $rectorConfig) : void {
    # https://symfony.com/blog/new-in-symfony-4-3-better-test-assertions
    $rectorConfig->rule(WebTestCaseAssertIsSuccessfulRector::class);
    $rectorConfig->rule(WebTestCaseAssertResponseCodeRector::class);
    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [
        new MethodCallRename('Symfony\\Component\\BrowserKit\\Response', 'getStatus', 'getStatusCode'),
        new MethodCallRename('Symfony\\Component\\Security\\Http\\Firewall', 'handleRequest', 'callListeners'),
        # https://github.com/symfony/http-kernel/blob/801b925e308518ddf821ba91952c41ae77c77507/Event/GetResponseForExceptionEvent.php#L55
        new MethodCallRename('Symfony\\Component\\HttpKernel\\Event\\GetResponseForExceptionEvent', 'getException', 'getThrowable'),
        # https://github.com/symfony/http-kernel/blob/801b925e308518ddf821ba91952c41ae77c77507/Event/GetResponseForExceptionEvent.php#L67
        new MethodCallRename('Symfony\\Component\\HttpKernel\\Event\\GetResponseForExceptionEvent', 'setException', 'setThrowable'),
    ]);
    $rectorConfig->rule(MakeDispatchFirstArgumentEventRector::class);
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        # https://symfony.com/blog/new-in-symfony-4-3-simpler-event-dispatching
        # Browser Kit
        'Symfony\\Component\\BrowserKit\\Client' => 'Symfony\\Component\\BrowserKit\\AbstractBrowser',
        # Cache
        # https://github.com/symfony/symfony/pull/29236
        'Symfony\\Component\\Cache\\Traits\\ApcuTrait\\ApcuCache' => 'Symfony\\Component\\Cache\\Traits\\ApcuTrait\\ApcuAdapter',
        'Symfony\\Component\\Cache\\Adapter\\SimpleCacheAdapter' => 'Symfony\\Component\\Cache\\Adapter\\Psr16Adapter',
        'Symfony\\Component\\Cache\\Simple\\ArrayCache' => 'Symfony\\Component\\Cache\\Adapter\\ArrayAdapter',
        'Symfony\\Component\\Cache\\Simple\\ChainCache' => 'Symfony\\Component\\Cache\\Adapter\\ChainAdapter',
        'Symfony\\Component\\Cache\\Simple\\DoctrineCache' => 'Symfony\\Component\\Cache\\Adapter\\DoctrineAdapter',
        'Symfony\\Component\\Cache\\Simple\\FilesystemCache' => 'Symfony\\Component\\Cache\\Adapter\\FilesystemAdapter',
        'Symfony\\Component\\Cache\\Simple\\MemcachedCache' => 'Symfony\\Component\\Cache\\Adapter\\MemcachedAdapter',
        'Symfony\\Component\\Cache\\Simple\\NullCache' => 'Symfony\\Component\\Cache\\Adapter\\NullAdapter',
        'Symfony\\Component\\Cache\\Simple\\PdoCache' => 'Symfony\\Component\\Cache\\Adapter\\PdoAdapter',
        'Symfony\\Component\\Cache\\Simple\\PhpArrayCache' => 'Symfony\\Component\\Cache\\Adapter\\PhpArrayAdapter',
        'Symfony\\Component\\Cache\\Simple\\PhpFilesCache' => 'Symfony\\Component\\Cache\\Adapter\\PhpFilesAdapter',
        'Symfony\\Component\\Cache\\Simple\\RedisCache' => 'Symfony\\Component\\Cache\\Adapter\\RedisAdapter',
        'Symfony\\Component\\Cache\\Simple\\TraceableCache' => 'Symfony\\Component\\Cache\\Adapter\\TraceableAdapterCache',
        'Symfony\\Component\\Cache\\Simple\\Psr6Cache' => 'Symfony\\Component\\Cache\\Psr16Cache',
        'Psr\\SimpleCache\\CacheInterface' => 'Symfony\\Contracts\\Cache\\CacheInterface',
        # EventDispatcher
        'Symfony\\Component\\HttpKernel\\Event\\FilterControllerArgumentsEvent' => 'Symfony\\Component\\HttpKernel\\Event\\ControllerArgumentsEvent',
        'Symfony\\Component\\HttpKernel\\Event\\FilterControllerEvent' => 'Symfony\\Component\\HttpKernel\\Event\\ControllerEvent',
        'Symfony\\Component\\HttpKernel\\Event\\FilterResponseEvent' => 'Symfony\\Component\\HttpKernel\\Event\\ResponseEvent',
        'Symfony\\Component\\HttpKernel\\Event\\GetResponseEvent' => 'Symfony\\Component\\HttpKernel\\Event\\RequestEvent',
        'Symfony\\Component\\HttpKernel\\Event\\GetResponseForControllerResultEvent' => 'Symfony\\Component\\HttpKernel\\Event\\ViewEvent',
        'Symfony\\Component\\HttpKernel\\Event\\GetResponseForExceptionEvent' => 'Symfony\\Component\\HttpKernel\\Event\\ExceptionEvent',
        'Symfony\\Component\\HttpKernel\\Event\\PostResponseEvent' => 'Symfony\\Component\\HttpKernel\\Event\\TerminateEvent',
        # has lowest priority, have to be last
        'Symfony\\Component\\EventDispatcher\\Event' => 'Symfony\\Contracts\\EventDispatcher\\Event',
        # MimeType
        'Symfony\\Component\\HttpFoundation\\File\\MimeType\\MimeTypeGuesserInterface' => 'Symfony\\Component\\Mime\\MimeTypesInterface',
        'Symfony\\Component\\HttpFoundation\\File\\MimeType\\ExtensionGuesserInterface' => 'Symfony\\Component\\Mime\\MimeTypesInterface',
        'Symfony\\Component\\HttpFoundation\\File\\MimeType\\MimeTypeExtensionGuesser' => 'Symfony\\Component\\Mime\\MimeTypes',
        'Symfony\\Component\\HttpFoundation\\File\\MimeType\\FileBinaryMimeTypeGuesser' => 'Symfony\\Component\\Mime\\FileBinaryMimeTypeGuesser',
        'Symfony\\Component\\HttpFoundation\\File\\MimeType\\FileinfoMimeTypeGuesser' => 'Symfony\\Component\\Mime\\FileinfoMimeTypeGuesser',
        # HttpKernel
        # @todo unpack after YAML to PHP migration, Symfony\Component\HttpKernel\Client: Symfony\Component\HttpKernel\HttpKernelBrowser
        'Symfony\\Component\\HttpKernel\\EventListener\\TranslatorListener' => 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleAwareListener',
        # Security
        'Symfony\\Component\\Security\\Core\\Encoder\\Argon2iPasswordEncoder' => 'Symfony\\Component\\Security\\Core\\Encoder\\SodiumPasswordEncoder',
        'Symfony\\Component\\Security\\Core\\Encoder\\BCryptPasswordEncoder' => 'Symfony\\Component\\Security\\Core\\Encoder\\NativePasswordEncoder',
    ]);
    # https://github.com/symfony/symfony/blob/4.4/UPGRADE-4.3.md#workflow
    $rectorConfig->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder('Symfony\\Component\\Workflow\\MarkingStore\\MarkingStoreInterface', 'setMarking', 2, 'context', [])]);
    $rectorConfig->ruleWithConfiguration(AddMethodParentCallRector::class, ['Symfony\\Component\\EventDispatcher\\EventDispatcher' => MethodName::CONSTRUCT]);
};
