<?php

declare(strict_types=1);

namespace Tempest\Console\Middleware;

use Tempest\Console\Actions\RenderConsoleCommand;
use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\Invocation;

final readonly class HelpMiddleware implements ConsoleMiddleware
{
    public function __construct(
        private Console $console,
    ) {
    }

    public function __invoke(Invocation $invocation, callable $next): void
    {
        if ($invocation->argumentBag->get('-h') || $invocation->argumentBag->get('help')) {
            $this->renderHelp($invocation->consoleCommand);

            return;
        }

        $next($invocation);
    }

    private function renderHelp(ConsoleCommand $consoleCommand): void
    {
        $this->console
            ->when($consoleCommand->help, fn (Console $console) => $console->writeln("<comment>{$consoleCommand->help}</comment>"))
            ->write('<h2>Usage</h2>');

        (new RenderConsoleCommand($this->console))($consoleCommand);

        foreach ($consoleCommand->getArgumentDefinitions() as $argumentDefinition) {
            $this->console
                ->writeln()
                ->when($argumentDefinition->help, fn (Console $console) => $console->writeln('<comment>' . $argumentDefinition->help . '</comment>'))
                ->write("<em>{$argumentDefinition->name}</em>")
                ->when($argumentDefinition->aliases !== [], fn (Console $console) => $console->write(' (' . implode(', ', $argumentDefinition->aliases) . ')'))
                ->when($argumentDefinition->description, fn (Console $console) => $console->write(' — ' . $argumentDefinition->description));
        }

        $this->console->writeln();
    }
}
