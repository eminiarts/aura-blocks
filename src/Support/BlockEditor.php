<?php

namespace Aura\Blocks\Support;

use Illuminate\Support\Str;

final class BlockEditor
{
    /** @param array<int, array<string, mixed>> $blocks */
    public function add(array $blocks, BlockDefinition $definition): array
    {
        $blocks[] = [
            'id' => (string) Str::uuid(),
            'type' => $definition->type(),
            'data' => $definition->defaults(),
        ];

        return array_values($blocks);
    }

    /** @param array<int, array<string, mixed>> $blocks */
    public function moveDown(array $blocks, int $index): array
    {
        if ($index < 0 || $index >= count($blocks) - 1) {
            return array_values($blocks);
        }

        [$blocks[$index], $blocks[$index + 1]] = [$blocks[$index + 1], $blocks[$index]];

        return array_values($blocks);
    }

    /** @param array<int, array<string, mixed>> $blocks */
    public function moveUp(array $blocks, int $index): array
    {
        if ($index <= 0 || $index >= count($blocks)) {
            return array_values($blocks);
        }

        [$blocks[$index], $blocks[$index - 1]] = [$blocks[$index - 1], $blocks[$index]];

        return array_values($blocks);
    }

    /** @param array<int, array<string, mixed>> $blocks */
    public function remove(array $blocks, int $index): array
    {
        if (array_key_exists($index, $blocks)) {
            unset($blocks[$index]);
        }

        return array_values($blocks);
    }
}
