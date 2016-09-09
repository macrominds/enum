<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    //->exclude('somedir')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->fixers(array('strict_param', 'short_array_syntax'))
    ->finder($finder)
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
;
