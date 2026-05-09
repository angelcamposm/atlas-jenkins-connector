<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP83Migration' => true,
        'declare_strict_types' => true,
        'header_comment' => [
            'header' => '',
        ],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
