<?php

declare(strict_types=1);

namespace Tempest\Console;

use ReflectionProperty;
use ReflectionNamedType;
use ReflectionParameter;

final readonly class ConsoleArgumentDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public mixed $default,
        public bool $hasDefault,
        public ?int $position,
        public ?string $description = null,
        public array $aliases = [],
        public ?string $help = null,
        public bool $isPositional = true,
    ) {
    }

    public static function fromParameter(ReflectionParameter $parameter, bool $asPositional = true): ConsoleArgumentDefinition
    {
        $attributes = $parameter->getAttributes(ConsoleArgument::class);

        /** @var ?ConsoleArgument $attribute */
        $attribute = ($attributes[0] ?? null)?->newInstance();

        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType) {
            $typeName = $type->getName();
        } else {
            $typeName = '';
        }

        return new ConsoleArgumentDefinition(
            name: $parameter->getName(),
            type: $typeName,
            default: $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
            hasDefault: $parameter->isDefaultValueAvailable(),
            position: $parameter->getPosition(),
            description: $attribute?->description,
            aliases: $attribute->aliases ?? [],
            help: $attribute?->help,
            isPositional: $asPositional,
        );
    }

    public function matchesArgument(ConsoleInputArgument $argument): bool
    {
        if ($this->isPositional && $argument->position === $this->position) {
            return true;
        }

        if (! $argument->name) {
            return false;
        }

        foreach ([$this->name, ...$this->aliases] as $alias) {
            if ($alias === $argument->name) {
                return true;
            }

            return false;
        }
    }
}
