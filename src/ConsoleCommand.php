<?php

declare(strict_types=1);

namespace Tempest\Console;

use Attribute;
use ReflectionMethod;
use Tempest\Support\ArrayHelper;
use Tempest\Console\Widgets\ConsoleWidget;

#[Attribute]
final class ConsoleCommand
{
    public ReflectionMethod $handler;

    /** @var string[] */
    public readonly array $help;

    public function __construct(
        private readonly ?string $name = null,
        public readonly ?string $description = null,
        /** @var string[] */
        public readonly array $aliases = [],
        /** @var string|string[] $help */
        string|array $help = [],

        /** @var class-string<ConsoleWidget>[] */
        public readonly array $widgets = [],
    ) {
        $this->help = ArrayHelper::wrap($help);
    }

    public function setHandler(ReflectionMethod $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        return $this->handler->getName() === '__invoke'
            ? strtolower($this->handler->getDeclaringClass()->getShortName())
            : strtolower($this->handler->getDeclaringClass()->getShortName() . ':' . $this->handler->getName());
    }

    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'handler_class' => $this->handler->getDeclaringClass()->getName(),
            'handler_method' => $this->handler->getName(),
            'aliases' => $this->aliases,
            'help' => $this->help,
            'widgets' => $this->widgets,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->handler = new ReflectionMethod(
            objectOrMethod: $data['handler_class'],
            method: $data['handler_method'],
        );
        $this->aliases = $data['aliases'];
        $this->help = $data['help'];
        $this->widgets = $data['widgets'];
    }

    /**
     * @return \Tempest\Console\ConsoleArgumentDefinition[]
     */
    public function getArgumentDefinitions(): array
    {
        $arguments = [];

        foreach ($this->handler->getParameters() as $parameter) {
            $arguments[$parameter->getName()] = ConsoleArgumentDefinition::fromParameter($parameter);
        }

        return $arguments;
    }
}
