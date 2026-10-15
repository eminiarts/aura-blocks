<?php

use Aura\Blocks\Fields\Blocks;
use Aura\Blocks\Support\BlockDefinition;
use Aura\Blocks\Support\BlockRegistry;
use Aura\Blocks\Support\BlockRenderer;
use Aura\Blocks\Tests\Fixtures\BlocksPage;
use Illuminate\Validation\ValidationException;

test('the package registers the Blocks field and built in types', function () {
    expect(app('aura')::getFields())->toContain(Blocks::class)
        ->and(array_keys(app(BlockRegistry::class)->registered()))->toBe(['text', 'image']);
});

test('the field validates and round trips ordered block data', function () {
    $field = BlocksPage::getFields()[1];
    $blocks = [
        [
            'id' => 'first',
            'type' => 'text',
            'data' => [
                'heading' => 'Introduction',
                'text' => 'Hello blocks',
                'discarded' => 'not part of the schema',
            ],
        ],
        [
            'id' => 'second',
            'type' => 'image',
            'data' => [
                'url' => 'https://example.com/still.jpg',
                'alt' => 'A still',
                'caption' => 'On location',
            ],
        ],
    ];

    $encoded = app(Blocks::class)->set(new BlocksPage, $field, $blocks);
    $decoded = app(Blocks::class)->get(Blocks::class, $encoded, $field);

    expect($decoded)->toHaveCount(2)
        ->and($decoded[0]['id'])->toBe('first')
        ->and($decoded[0]['type'])->toBe('text')
        ->and($decoded[0]['data'])->not->toHaveKey('discarded')
        ->and($decoded[1]['id'])->toBe('second')
        ->and($decoded[1]['type'])->toBe('image');
});

test('nested field validation rejects missing values and unsupported types', function () {
    $field = BlocksPage::getFields()[1];

    expect(fn () => app(Blocks::class)->set(new BlocksPage, $field, [
        ['type' => 'text', 'data' => ['heading' => 'Missing body']],
    ]))->toThrow(ValidationException::class);

    expect(fn () => app(Blocks::class)->set(new BlocksPage, $field, [
        ['type' => 'video', 'data' => ['url' => 'https://example.com/movie.mp4']],
    ]))->toThrow(ValidationException::class);
});

test('Aura meta persistence keeps block values and order after reload', function () {
    $blocks = [
        ['id' => 'one', 'type' => 'text', 'data' => ['heading' => 'One', 'text' => 'First']],
        ['id' => 'two', 'type' => 'image', 'data' => ['url' => 'https://example.com/two.jpg', 'alt' => 'Two', 'caption' => null]],
    ];

    $page = BlocksPage::create([
        'title' => 'Blocks page',
        'type' => 'BlocksPage',
        'fields' => ['content_blocks' => $blocks],
    ]);

    $reloaded = BlocksPage::withoutGlobalScopes()->findOrFail($page->getKey());

    expect($reloaded->content_blocks)->toBe($blocks)
        ->and($reloaded->fields['content_blocks'])->toBe($blocks);
});

test('renderer preserves order escapes built in text and supports host templates', function () {
    view()->addNamespace('aura-blocks-tests', __DIR__.'/../views');

    app(BlockRegistry::class)->register(
        BlockDefinition::make('quote', 'Quote')
            ->fields([
                [
                    'name' => 'Quote',
                    'slug' => 'quote',
                    'type' => 'Aura\\Base\\Fields\\Textarea',
                    'validation' => 'required|string',
                ],
            ])
            ->view('aura-blocks-tests::quote'),
    );

    $rendered = (string) app(BlockRenderer::class)->render([
        ['type' => 'text', 'data' => ['heading' => '<script>bad()</script>', 'text' => 'First']],
        ['type' => 'quote', 'data' => ['quote' => 'Second']],
    ], ['blocks' => ['text', 'quote']]);

    expect($rendered)
        ->toContain('&lt;script&gt;bad()&lt;/script&gt;')
        ->not->toContain('<script>')
        ->and(strpos($rendered, 'First'))->toBeLessThan(strpos($rendered, 'Second'));
});
