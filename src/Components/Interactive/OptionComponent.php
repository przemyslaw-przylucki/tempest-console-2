<?php

declare(strict_types=1);

namespace Tempest\Console\Components\Interactive;

use Tempest\Console\Components\HasStaticComponent;
use Tempest\Console\Components\InteractiveComponent;
use Tempest\Console\Components\Static\StaticOptionComponent;
use Tempest\Console\Components\StaticComponent;
use Tempest\Console\HandlesKey;
use Tempest\Console\Key;

final class OptionComponent implements InteractiveComponent, HasStaticComponent
{
    public int $selectedOption;

    public function __construct(
        public string $question,
        public array $options,
        public ?string $default = null,
    )
    {
        if ($this->default) {
            foreach ($this->options as $key => $option) {
                if ($option === $this->default) {
                    $this->selectedOption = $key;
                    break;
                }
            }
        }

        if (! isset($this->selectedOption)) {
            $this->selectedOption = array_key_first($this->options);
        }
    }

    public function render(): string
    {
        $output = "<question>{$this->question}</question>";

        foreach ($this->options as $key => $option) {
            $output .= PHP_EOL;
            $output .= $this->isSelected($key) ? "[x] <em>{$option}</em>" : "[ ] {$option}";
        }

        return $output . PHP_EOL . PHP_EOL . "Press <em>enter</em> to confirm, <em>ctrl+c</em> to cancel" . PHP_EOL;
    }

    public function isSelected(int $key): bool
    {
        return $this->selectedOption === $key;
    }

    #[HandlesKey(Key::ENTER)]
    public function enter(): string
    {
        return $this->options[$this->selectedOption] ?? '';
    }

    #[HandlesKey(Key::UP)]
    #[HandlesKey(Key::LEFT)]
    public function up(): void
    {
        $this->selectedOption = $this->selectedOption - 1;

        if ($this->selectedOption < 0) {
            $this->selectedOption = count($this->options) - 1;
        }
    }

    #[HandlesKey(Key::DOWN)]
    #[HandlesKey(Key::RIGHT)]
    public function down(): void
    {
        $this->selectedOption = $this->selectedOption + 1;

        if ($this->selectedOption > count($this->options) - 1) {
            $this->selectedOption = 0;
        }
    }

    public function getStaticComponent(): StaticComponent
    {
        return new StaticOptionComponent(
            question: $this->question,
            options: $this->options,
            default: $this->default,
        );
    }
}