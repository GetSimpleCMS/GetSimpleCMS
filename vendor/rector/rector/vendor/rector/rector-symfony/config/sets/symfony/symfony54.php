<?php

declare (strict_types=1);
namespace RectorPrefix202212;

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameClassAndConstFetch;
use Rector\Renaming\ValueObject\RenameClassConstFetch;
use Rector\Symfony\Set\SymfonySetList;
# https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.4.md
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->sets([SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES]);
    // @see https://symfony.com/blog/new-in-symfony-5-4-nested-validation-attributes
    // @see https://github.com/symfony/symfony/pull/41994
    $rectorConfig->ruleWithConfiguration(AnnotationToAttributeRector::class, [new AnnotationToAttribute('Symfony\\Component\\Validator\\Constraints\\All'), new AnnotationToAttribute('Symfony\\Component\\Validator\\Constraints\\Collection'), new AnnotationToAttribute('Symfony\\Component\\Validator\\Constraints\\AtLeastOneOf'), new AnnotationToAttribute('Symfony\\Component\\Validator\\Constraints\\Sequentially')]);
    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [
        // @see https://github.com/symfony/symfony/pull/42582
        new MethodCallRename('Symfony\\Bundle\\SecurityBundle\\Security\\FirewallConfig', 'getListeners', 'getAuthenticators'),
        // @see https://github.com/symfony/symfony/pull/41754
        new MethodCallRename('Symfony\\Bundle\\SecurityBundle\\DependencyInjection\\SecurityExtension', 'addSecurityListenerFactory', 'addAuthenticatorFactory'),
    ]);
    $rectorConfig->ruleWithConfiguration(RenameClassConstFetchRector::class, [
        new RenameClassAndConstFetch('Symfony\\Component\\Security\\Core\\AuthenticationEvents', 'AUTHENTICATION_SUCCESS', 'Symfony\\Component\\Security\\Core\\Event\\AuthenticationSuccessEvent', 'class'),
        new RenameClassAndConstFetch('Symfony\\Component\\Security\\Core\\AuthenticationEvents', 'AUTHENTICATION_FAILURE', 'Symfony\\Component\\Security\\Core\\Event\\AuthenticationFailureEvent', 'class'),
        // @see https://github.com/symfony/symfony/pull/42510
        new RenameClassConstFetch('Symfony\\Component\\Security\\Core\\Authorization\\Voter\\AuthenticatedVoter', 'IS_ANONYMOUS', 'PUBLIC_ACCESS'),
        new RenameClassConstFetch('Symfony\\Component\\Security\\Core\\Authorization\\Voter\\AuthenticatedVoter', 'IS_AUTHENTICATED_ANONYMOUSLY', 'PUBLIC_ACCESS'),
    ]);
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        // @see https://github.com/symfony/symfony/pull/42050
        'Symfony\\Component\\Security\\Http\\Event\\DeauthenticatedEvent' => 'Symfony\\Component\\Security\\Http\\Event\\TokenDeauthenticatedEvent',
        // @see https://github.com/symfony/symfony/pull/42965
        'Symfony\\Component\\Cache\\Adapter\\DoctrineAdapter' => 'Doctrine\\Common\\Cache\\Psr6\\CacheAdapter',
        // @see https://github.com/symfony/symfony/pull/45615
        'Symfony\\Component\\HttpKernel\\EventListener\\AbstractTestSessionListener' => 'Symfony\\Component\\HttpKernel\\EventListener\\AbstractSessionListener',
        'Symfony\\Component\\HttpKernel\\EventListener\\TestSessionListener' => 'Symfony\\Component\\HttpKernel\\EventListener\\SessionListener',
        // @see https://github.com/symfony/symfony/pull/44271
        'Symfony\\Component\\Notifier\\Bridge\\Nexmo\\NexmoTransportFactory' => 'Symfony\\Component\\Notifier\\Bridge\\Vonage\\VonageTransportFactory',
        'Symfony\\Component\\Notifier\\Bridge\\Nexmo\\NexmoTransport' => 'Symfony\\Component\\Notifier\\Bridge\\Vonage\\VonageTransport',
    ]);
};
