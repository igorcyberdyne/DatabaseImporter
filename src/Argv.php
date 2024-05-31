<?php

namespace DatabaseImporter;

use Symfony\Component\Console\Input\ArgvInput;

class Argv extends ArgvInput
{
    public function __construct(?array $argv = null)
    {
        parent::__construct($argv);
    }
}