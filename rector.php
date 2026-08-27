<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/includes',
        __DIR__.'/admin',
    ])
    ->withSkip([
        __DIR__.'/vendor',
        __DIR__.'/node_modules',
        __DIR__.'/dist',
        __DIR__.'/config',
    ])
    // PHP 8.2 features
    ->withPhpSets(php82: true)
    // Modernize code to PHP 8.2
    ->withSets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::CODING_STYLE,
        SetList::INSTANCEOF,
        SetList::STRICT_BOOLEANS,
    ])
    // Parallel processing for faster analysis
    ->withParallel()
    // Cache directory
    ->withCache(__DIR__.'/.rector-cache');
