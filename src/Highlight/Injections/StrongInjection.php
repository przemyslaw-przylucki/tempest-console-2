<?php

declare(strict_types=1);

namespace Tempest\Console\Highlight\Injections;

use Tempest\Console\Highlight\ConsoleTokenType;
use Tempest\Console\Highlight\IsTagInjection;
use Tempest\Highlight\Injection;

final readonly class StrongInjection implements Injection
{
    use IsTagInjection;

    public function getTag(): string
    {
        return 'strong';
    }

    public function getTokenType(): ConsoleTokenType
    {
        return ConsoleTokenType::STRONG;
    }
}
