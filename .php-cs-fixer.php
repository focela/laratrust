<?php
/*
 * Copyright (c) 2024 Focela Technologies. All rights reserved.
 * Use of this source code is governed by a MIT style
 * license that can be found in the LICENSE file.
 */

// Directories to not scan
use Focela\PhpCsFixer\Config;

$excludeDirs = [
    'vendor/',
];

// Files to not scan
$excludeFiles = [];

// Create a new Symfony Finder instance
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude($excludeDirs)
    ->ignoreDotFiles(true)->ignoreVCS(true)
    ->filter(function (SplFileInfo $file) use ($excludeFiles) {
        return !in_array($file->getRelativePathName(), $excludeFiles);
    });

return (new Config())
    ->setFinder($finder)
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->withPHPUnitRules();
