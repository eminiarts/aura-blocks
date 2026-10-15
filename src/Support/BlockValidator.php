<?php

namespace Aura\Blocks\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class BlockValidator
{
    public function __construct(private readonly BlockRegistry $registry) {}

    /** @return array<int, array{id: string, type: string, data: array<string, mixed>}> */
    public function validate(?array $blocks, array $field): array
    {
        $blocks ??= [];
        $definitions = $this->registry->forField($field);
        $slug = (string) ($field['slug'] ?? 'blocks');
        $root = 'form.fields.'.$slug;
        $rootRules = ['array'];

        if (($field['min'] ?? null) !== null && (int) $field['min'] > 0) {
            $rootRules[] = 'min:'.(int) $field['min'];
        }

        if (($field['max'] ?? null) !== null && (int) $field['max'] > 0) {
            $rootRules[] = 'max:'.(int) $field['max'];
        }

        $rules = [
            $root => $rootRules,
            $root.'.*.id' => ['nullable', 'string', 'max:100'],
            $root.'.*.type' => ['required', 'string', Rule::in(array_keys($definitions))],
            $root.'.*.data' => ['required', 'array'],
        ];

        foreach ($blocks as $index => $block) {
            if (! is_array($block) || ! isset($block['type']) || ! is_string($block['type'])) {
                continue;
            }

            $definition = $definitions[$block['type']] ?? null;

            if (! $definition) {
                continue;
            }

            foreach ($definition->fieldsList() as $blockField) {
                $validation = $blockField['validation'] ?? 'nullable';
                $rules[$root.'.'.$index.'.data.'.$blockField['slug']] = $validation ?: 'nullable';
            }
        }

        $payload = [];
        Arr::set($payload, $root, $blocks);

        validator($payload, $rules)->validate();

        return collect($blocks)->map(function (array $block) use ($definitions): array {
            $definition = $definitions[$block['type']];
            $allowed = collect($definition->fieldsList())->pluck('slug')->all();

            return [
                'id' => (string) ($block['id'] ?? Str::uuid()),
                'type' => $definition->type(),
                'data' => Arr::only((array) ($block['data'] ?? []), $allowed),
            ];
        })->values()->all();
    }
}
