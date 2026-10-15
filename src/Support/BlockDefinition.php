<?php

namespace Aura\Blocks\Support;

use InvalidArgumentException;

final class BlockDefinition
{
    /** @var array<int, array<string, mixed>> */
    private array $fields = [];

    private ?string $icon = null;

    private ?string $view = null;

    private function __construct(
        private readonly string $type,
        private readonly string $label,
    ) {
        if ($type === '') {
            throw new InvalidArgumentException('A block type cannot be empty.');
        }
    }

    public static function fromArray(string $type, array $definition): self
    {
        return self::make($type, (string) ($definition['label'] ?? str($type)->headline()))
            ->fields((array) ($definition['fields'] ?? []))
            ->icon($definition['icon'] ?? null)
            ->view($definition['view'] ?? null);
    }

    public static function make(string $type, string $label): self
    {
        return new self($type, $label);
    }

    /** @return array<string, mixed> */
    public function defaults(): array
    {
        return collect($this->fields)
            ->mapWithKeys(fn (array $field): array => [
                (string) $field['slug'] => $field['default'] ?? null,
            ])
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    public function fieldsList(): array
    {
        return $this->fields;
    }

    /** @param array<int, array<string, mixed>> $fields */
    public function fields(array $fields): self
    {
        foreach ($fields as $field) {
            if (empty($field['slug']) || empty($field['type'])) {
                throw new InvalidArgumentException("Block [{$this->type}] fields require a slug and Aura field type.");
            }
        }

        $clone = clone $this;
        $clone->fields = array_values($fields);

        return $clone;
    }

    public function icon(?string $icon): self
    {
        $clone = clone $this;
        $clone->icon = $icon;

        return $clone;
    }

    public function iconName(): ?string
    {
        return $this->icon;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function view(?string $view): self
    {
        $clone = clone $this;
        $clone->view = $view;

        return $clone;
    }

    public function viewName(): ?string
    {
        return $this->view;
    }
}
