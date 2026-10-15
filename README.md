# Aura Blocks

Aura Blocks adds a reusable structured-content field to Aura CMS. A host Resource chooses the allowed block types; editors can add, edit, reorder, and remove blocks without depending on Blog, Page, Author, Category, or any other domain model.

The package name is `eminiarts/aura-blocks`. Its intended repository is `https://github.com/eminiarts/aura-blocks`.

## Installation

```bash
composer require eminiarts/aura-blocks
```

Laravel package discovery registers the service provider, the `Aura\Blocks\Fields\Blocks` field, its Livewire editor, the built-in block types, and the package views. No migrations are required because the field uses Aura's normal Resource/meta storage.

## Minimal Resource field

```php
use Aura\Blocks\Fields\Blocks;

public static function getFields(): array
{
    return [
        [
            'name' => 'Content blocks',
            'slug' => 'content_blocks',
            'type' => Blocks::class,
            'blocks' => ['text', 'image'],
            'validation' => 'nullable|array',
            'on_forms' => true,
            'on_view' => true,
        ],
    ];
}
```

The built-in set stays deliberately small:

- `text`: optional heading plus required text.
- `image`: image URL, alternative text, and optional caption.

Each block's inputs are ordinary Aura field definitions. This keeps the API familiar and lets custom blocks use `Text`, `Textarea`, `Number`, `Select`, and other field primitives that work in an embedded Livewire form. A block that needs a specialized picker can provide its own Aura Field component.

## Registering a custom block

Register application-specific types from a service provider before the field is rendered:

```php
use Aura\Base\Fields\Text;
use Aura\Base\Fields\Textarea;
use Aura\Blocks\Support\BlockDefinition;
use Aura\Blocks\Support\BlockRegistry;

public function boot(BlockRegistry $blocks): void
{
    $blocks->register(
        BlockDefinition::make('quote', 'Quote')
            ->fields([
                [
                    'name' => 'Quote',
                    'slug' => 'quote',
                    'type' => Textarea::class,
                    'validation' => 'required|string|max:2000',
                ],
                [
                    'name' => 'Attribution',
                    'slug' => 'attribution',
                    'type' => Text::class,
                    'validation' => 'nullable|string|max:255',
                ],
            ])
            ->view('content.blocks.quote'),
    );
}
```

Then allow it on a Resource field:

```php
'blocks' => ['text', 'image', 'quote'],
```

The rendering view receives `$block` (the block's `data` array), `$definition`, and `$resource`. Host views are trusted application templates and remain fully overrideable.

## Saved content format

Blocks are stored as ordered JSON. Each row has a stable editor ID, a registered type, and schema-filtered data:

```json
[
  {
    "id": "5ae1f604-99f4-47dd-9827-dc40b2632a5e",
    "type": "text",
    "data": {
      "heading": "Behind the scenes",
      "text": "A short production note."
    }
  },
  {
    "id": "69d69626-c457-43cc-bc9b-9866f87748e5",
    "type": "image",
    "data": {
      "url": "https://example.com/still.jpg",
      "alt": "A film still",
      "caption": "Filmed on location."
    }
  }
]
```

Saving validates the allowed type and every nested field rule. Unknown types are rejected, undeclared data keys are removed, and array order is preserved when the Resource is reopened.

## Rendering

Aura's normal display path renders the saved value with each type's configured Blade view:

```blade
{!! $movie->display('content_blocks') !!}
```

For a custom frontend or a value that is not being displayed through an Aura Resource:

```blade
{!! app(\Aura\Blocks\Support\BlockRenderer::class)
    ->render($blocks, ['blocks' => ['text', 'image']], $movie) !!}
```

Publish package views when an application wants to customize the built-in templates:

```bash
php artisan vendor:publish --tag=aura-blocks-views
```

## Validation and limits

Use the field's normal validation plus optional block-count limits:

```php
[
    'name' => 'Content blocks',
    'slug' => 'content_blocks',
    'type' => Blocks::class,
    'blocks' => ['text', 'image'],
    'validation' => 'required|array',
    'min' => 1,
    'max' => 12,
]
```

## Testing

```bash
composer test
```

The package test host covers field registration, add/edit/reorder/remove behavior, validation, Aura meta persistence and reload, custom templates, output escaping, and rendering order.

## License

Aura Blocks is open-sourced software licensed under the [MIT license](LICENSE.md).
