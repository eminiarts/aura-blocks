<?php

namespace Aura\Blocks\Support;

use InvalidArgumentException;

final class BlockRegistry
{
    /** @var array<string, BlockDefinition> */
    private array $definitions = [];

    public function get(string $type): ?BlockDefinition
    {
        return $this->definitions[$type] ?? null;
    }

    public function register(BlockDefinition|array $definition, ?string $type = null): self
    {
        if (is_array($definition)) {
            if (! $type) {
                throw new InvalidArgumentException('An array block definition requires a type.');
            }

            $definition = BlockDefinition::fromArray($type, $definition);
        }

        $this->definitions[$definition->type()] = $definition;

        return $this;
    }

    /** @return array<string, BlockDefinition> */
    public function forField(array $field): array
    {
        $requested = $field['blocks'] ?? ['text', 'image'];

        if (is_string($requested)) {
            $requested = array_values(array_filter(array_map('trim', explode(',', $requested))));
        }

        if (! is_array($requested)) {
            return [];
        }

        $resolved = [];

        foreach ($requested as $key => $definition) {
            if ($definition instanceof BlockDefinition) {
                $resolved[$definition->type()] = $definition;

                continue;
            }

            if (is_string($definition)) {
                if ($registered = $this->get($definition)) {
                    $resolved[$registered->type()] = $registered;
                }

                continue;
            }

            if (is_string($key) && is_array($definition)) {
                $resolved[$key] = BlockDefinition::fromArray($key, $definition);
            }
        }

        return $resolved;
    }

    /** @return array<string, BlockDefinition> */
    public function registered(): array
    {
        return $this->definitions;
    }
}
