<?php

declare(strict_types=1);

namespace Tempest\Console;

use Tempest\Console\Exceptions\InvalidCommandException;
use Tempest\Console\Input\ConsoleArgumentBag;
use Tempest\Console\Input\ConsoleInputArgument;

final class ConsoleInputBuilder
{
    public function __construct(
        protected ConsoleCommand $command,
        protected ConsoleArgumentBag $argumentBag,
    ) {
    }

    /**
     * @return array<ConsoleInputArgument>
     */
    public function build(): array
    {
        $validArguments = [];
        $invalidArguments = [];

        $argumentDefinitions = $this->command->getArgumentDefinitions();

        foreach ($argumentDefinitions as $argumentDefinition) {
            $value = $this->argumentBag->findFor($argumentDefinition);

            if ($value === null) {
                $invalidArguments[] = $argumentDefinition;

                continue;
            }

            $validArguments[] = $value;
        }

        if (count($invalidArguments)) {
            throw new InvalidCommandException(
                $this->command,
                $invalidArguments
            );
        }

        return array_map(
            callback: fn (ConsoleInputArgument $argument) => $argument->value,
            array: $validArguments,
        );
    }
}
