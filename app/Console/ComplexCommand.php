<?php

namespace App\Console;

use Tempest\Console\ConsoleCommand;

final readonly class ComplexCommand
{
    #[ConsoleCommand('complex')]
    public function __invoke(string $a, string $b, string $c)
    {

    }
}