<?php

namespace Aura\Blocks\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

final class BlockRenderer
{
    public function __construct(private readonly BlockRegistry $registry) {}

    public function render(array|string|null $blocks, array $field = [], ?object $resource = null): Htmlable
    {
        if (is_string($blocks)) {
            $blocks = json_decode($blocks, true);
        }

        if (! is_array($blocks)) {
            return new HtmlString('');
        }

        $definitions = $this->registry->forField($field);
        $html = collect($blocks)->map(function (mixed $block) use ($definitions, $resource): string {
            if (! is_array($block) || ! isset($block['type'])) {
                return '';
            }

            $definition = $definitions[$block['type']] ?? null;

            if (! $definition || ! $definition->viewName()) {
                return '';
            }

            return view($definition->viewName(), [
                'block' => (array) ($block['data'] ?? []),
                'definition' => $definition,
                'resource' => $resource,
            ])->render();
        })->implode('');

        return new HtmlString($html);
    }
}
