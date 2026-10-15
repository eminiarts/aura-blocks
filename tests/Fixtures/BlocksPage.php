<?php

namespace Aura\Blocks\Tests\Fixtures;

use Aura\Base\Resource;
use Aura\Blocks\Fields\Blocks;

class BlocksPage extends Resource
{
    public static string $type = 'BlocksPage';

    public static ?string $slug = 'blocks-page';

    public static function getFields(): array
    {
        return [
            [
                'name' => 'Title',
                'slug' => 'title',
                'type' => 'Aura\\Base\\Fields\\Text',
                'validation' => 'required',
            ],
            [
                'name' => 'Content',
                'slug' => 'content_blocks',
                'type' => Blocks::class,
                'blocks' => ['text', 'image'],
                'validation' => 'nullable|array',
            ],
        ];
    }
}
