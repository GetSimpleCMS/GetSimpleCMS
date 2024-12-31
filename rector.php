<?php

// declare(strict_types=1);

// use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;
// use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

// remove 
// AddLiteralSeparatorToNumberRector

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/admin',
        __DIR__ . '/plugins',
        __DIR__ . '/extend',
    ]);

    // Set DEAD_CODE

    // register a single rule
    // $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    // $rectorConfig->rule(RemoveUnusedVariableAssignRector::class);
    // $rectorConfig->import(SetList::PHP_72);
    // $rectorConfig->import(SetList::PHP_73);
    $rectorConfig->import(SetList::PHP_74);
    $rectorConfig->import(SetList::PHP_80);
    // define sets of rules
    //    $rectorConfig->sets([
    //        LevelSetList::UP_TO_PHP_81
    //    ]);
    
    // ->withSkip([
    //     Rector\Php54\Rector\Array_\LongArrayToShortArrayRector::class,
    // ])
    // ->withPreparedSets(deadCode: true);
    // // A. whole set
    // ->withPreparedSets(typeDeclarations: true)
    // // B. or few rules
    // ->withRules([
    //     TypedPropertyFromAssignsRector::class
    // ]);    
};

// use Rector\Config\RectorConfig;
// use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

// return RectorConfig::configure()
//     // A. whole set
//     ->withPreparedSets(typeDeclarations: true)
//     // B. or few rules
//     ->withRules([
//         TypedPropertyFromAssignsRector::class
//     ]);


?>