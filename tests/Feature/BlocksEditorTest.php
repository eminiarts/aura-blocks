<?php

use Aura\Base\AuraServiceProvider;
use Aura\Blocks\Livewire\BlocksEditor;
use Livewire\Livewire;

test('an editor can add edit reorder and remove blocks', function () {
    $component = Livewire::test(BlocksEditor::class, [
        'field' => ['slug' => 'content_blocks', 'blocks' => ['text', 'image']],
        'value' => [],
    ])
        ->call('addBlock', 'text')
        ->assertSet('value.0.type', 'text')
        ->set('form.fields.blocks.0.data.heading', 'First block')
        ->set('form.fields.blocks.0.data.text', 'First text')
        ->call('addBlock', 'image')
        ->set('form.fields.blocks.1.data.url', 'https://example.com/still.jpg')
        ->assertSet('value.0.data.heading', 'First block')
        ->assertSet('value.1.type', 'image')
        ->call('moveBlockUp', 1)
        ->assertSet('value.0.type', 'image')
        ->assertSet('value.1.type', 'text')
        ->call('moveBlockDown', 0)
        ->assertSet('value.0.type', 'text')
        ->call('removeBlock', 1)
        ->assertCount('value', 1)
        ->assertSet('value.0.type', 'text');

    expect($component->get('value.0.id'))->toBeString()->not->toBeEmpty();
});

test('the editor refuses block types that are not enabled on the field', function () {
    Livewire::test(BlocksEditor::class, [
        'field' => ['slug' => 'content_blocks', 'blocks' => ['text']],
        'value' => [],
    ])
        ->call('addBlock', 'image')
        ->assertHasErrors('blockType')
        ->assertSet('value', []);
});

test('the editor uses Aura component classes so it follows the appearance settings', function () {
    $editor = Livewire::test(BlocksEditor::class, [
        'field' => ['slug' => 'content_blocks', 'blocks' => ['text']],
        'value' => [],
    ])
        ->assertSeeHtml('data-aura-blocks-empty')
        ->call('addBlock', 'text')
        ->assertSeeHtml('border aura-group-section')
        ->assertSeeHtml('aura-layout-row')
        ->assertDontSeeHtml('rounded-xl');

    // The Add button and the block-type select are Aura's own components. They carry the token
    // classes (aura-button, aura-control-base) only in aura-cms releases with the design tokens;
    // 0.2.0 has neither, so check them only when the installed aura-cms ships the tokens.
    $auraCss = (string) @file_get_contents(dirname((new ReflectionClass(AuraServiceProvider::class))->getFileName(), 2).'/resources/css/app.css');

    if (str_contains($auraCss, '--aura-control-height')) {
        $editor->assertSeeHtml('aura-button')->assertSeeHtml('aura-control-base');
    }
});
