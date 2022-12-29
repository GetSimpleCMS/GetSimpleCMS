<?php

declare (strict_types=1);
namespace RectorPrefix202212;

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Symfony\Rector\MethodCall\ChangeCollectionTypeOptionNameFromTypeToEntryTypeRector;
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->rule(ChangeCollectionTypeOptionNameFromTypeToEntryTypeRector::class);
    // @see https://symfony.com/blog/new-in-symfony-2-7-form-and-validator-updates#deprecated-setdefaultoptions-in-favor-of-configureoptions
    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [new MethodCallRename('Symfony\\Component\\Form\\AbstractType', 'setDefaultOptions', 'configureOptions')]);
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, ['Symfony\\Component\\OptionsResolver\\OptionsResolverInterface' => 'Symfony\\Component\\OptionsResolver\\OptionsResolver']);
};
