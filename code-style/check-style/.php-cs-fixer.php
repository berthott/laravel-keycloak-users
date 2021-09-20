<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('bin') // exclude directories
    ->notPath('_ide_helper_models.php')
    ->notPath('_ide_helper.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true, // @PSR12
    'strict_param' => true,

    "phpdoc_annotation_without_dot" => true,
    "self_accessor" => true,
    "combine_consecutive_unsets" => true
])
    ->setFinder($finder);
