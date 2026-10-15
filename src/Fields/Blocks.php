<?php

namespace Aura\Blocks\Fields;

use Aura\Base\Fields\Field;
use Aura\Blocks\Support\BlockRenderer;
use Aura\Blocks\Support\BlockValidator;

class Blocks extends Field
{
    public $edit = 'aura-blocks::fields.blocks';

    public $optionGroup = 'Structure Fields';

    public bool $rawHtmlDisplay = true;

    public string $type = 'input';

    public $view = 'aura::fields.view-value';

    public function display($field, $value, $model)
    {
        return app(BlockRenderer::class)->render($value, $field, $model);
    }

    public function get($class, $value, $field = null)
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }

    public function getFields()
    {
        return array_merge(parent::getFields(), [
            [
                'name' => 'Blocks',
                'type' => 'Aura\\Base\\Fields\\Tab',
                'slug' => 'blocks-tab',
            ],
            [
                'name' => 'Block types',
                'type' => 'Aura\\Base\\Fields\\Text',
                'slug' => 'blocks',
                'instructions' => 'Comma-separated registered block type IDs, for example: text,image.',
                'default' => 'text,image',
            ],
            [
                'name' => 'Minimum blocks',
                'type' => 'Aura\\Base\\Fields\\Number',
                'slug' => 'min',
                'default' => 0,
                'style' => ['width' => '50'],
            ],
            [
                'name' => 'Maximum blocks',
                'type' => 'Aura\\Base\\Fields\\Number',
                'slug' => 'max',
                'default' => 0,
                'style' => ['width' => '50'],
            ],
        ]);
    }

    public function set($post, $field, $value)
    {
        $blocks = is_array($value) ? $value : [];

        return json_encode(
            app(BlockValidator::class)->validate($blocks, $field),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }
}
