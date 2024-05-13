<?php

namespace Tempest\Console\Components\Static;

use Tempest\Console\Components\StaticComponent;
use Tempest\Console\Console;

final readonly class StaticMultipleChoiceComponent implements StaticComponent
{
    public function __construct(
        public string $question,
        public array $options,
    ) {}

    public function render(Console $console): array
    {
        do {
            $answer = $this->askQuestion($console);

            $answerAsString = implode(', ', $answer);

            $confirm = $console->confirm(
                question: "You picked {$answerAsString}; continue?",
                default: true,
            );
        } while ($confirm === false);

        return $answer;
    }

    private function askQuestion(Console $console): array
    {
        $console->write("<h2>{$this->question}</h2> ");

        $parsedOptions = [];

        foreach ($this->options as $option) {
            $parsedOptions[] = "- {$option}";
        }

        $console->write(PHP_EOL . implode(PHP_EOL, $parsedOptions) . PHP_EOL);

        $answers = explode(',', $console->readln());

        $validAnswers = [];

        foreach ($answers as $answer) {
            $answer = trim($answer);

            if (! in_array($answer, $this->options)) {
                continue;
            }

            $validAnswers[] = $answer;
        }

        return $validAnswers;
    }
}