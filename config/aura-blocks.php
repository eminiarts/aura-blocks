<?php

use Aura\Base\Fields\Text;
use Aura\Base\Fields\Textarea;

return [
    'types' => [
        'text' => [
            'label' => 'Text',
            'icon' => 'bars-3-bottom-left',
            'fields' => [
                [
                    'name' => 'Heading',
                    'slug' => 'heading',
                    'type' => Text::class,
                    'validation' => 'nullable|string|max:255',
                ],
                [
                    'name' => 'Text',
                    'slug' => 'text',
                    'type' => Textarea::class,
                    'validation' => 'required|string|max:10000',
                    'rows' => 6,
                ],
            ],
            'view' => 'aura-blocks::blocks.text',
        ],
        'image' => [
            'label' => 'Image',
            'icon' => 'photo',
            'fields' => [
                [
                    'name' => 'Image URL',
                    'slug' => 'url',
                    'type' => Text::class,
                    'validation' => 'required|url|max:2048',
                    'placeholder' => 'https://example.com/image.jpg',
                ],
                [
                    'name' => 'Alternative text',
                    'slug' => 'alt',
                    'type' => Text::class,
                    'validation' => 'nullable|string|max:255',
                ],
                [
                    'name' => 'Caption',
                    'slug' => 'caption',
                    'type' => Textarea::class,
                    'validation' => 'nullable|string|max:1000',
                    'rows' => 3,
                ],
            ],
            'view' => 'aura-blocks::blocks.image',
        ],
    ],
];
