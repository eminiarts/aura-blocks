{{--
    Styles come from Aura's component classes (aura-cms resources/css/app.css), so the editor follows
    the appearance settings: each block is a panel inside the field's panel (.aura-group-section:
    panel padding and radius, section gap below), its fields sit in an .aura-layout-row, and the
    controls are Aura buttons and selects at the base control height with the control radius.
    This package has no CSS build of its own: use only classes that Aura's prebuilt CSS contains.
--}}
<div data-aura-blocks-editor>
    <div>
        @forelse ($blocks as $index => $block)
            @php
                $definition = $definitions[$block['type'] ?? ''] ?? null;
                $blockId = $block['id'] ?? $index;
            @endphp

            @if ($definition)
                <section
                    class="border aura-group-section"
                    wire:key="aura-block-{{ $blockId }}"
                    data-block-type="{{ $definition->type() }}"
                >
                    <header class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold aura-chip-primary">
                                {{ $index + 1 }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $definition->label() }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $definition->type() }}</p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <x-aura::button.transparent
                                size="xs"
                                wire:click="moveBlockUp({{ $index }})"
                                :disabled="$index === 0"
                                aria-label="{{ __('Move block up') }}"
                            >
                                <x-slot:icon><x-aura::icon icon="chevron-up" size="xs" /></x-slot:icon>
                            </x-aura::button.transparent>
                            <x-aura::button.transparent
                                size="xs"
                                wire:click="moveBlockDown({{ $index }})"
                                :disabled="$index === count($blocks) - 1"
                                aria-label="{{ __('Move block down') }}"
                            >
                                <x-slot:icon><x-aura::icon icon="chevron-down" size="xs" /></x-slot:icon>
                            </x-aura::button.transparent>
                            <x-aura::button.transparent
                                size="xs"
                                class="hover:text-red-500"
                                wire:click="removeBlock({{ $index }})"
                                wire:confirm="{{ __('Remove this block?') }}"
                                aria-label="{{ __('Remove block') }}"
                            >
                                <x-slot:icon><x-aura::icon icon="trash" size="xs" /></x-slot:icon>
                            </x-aura::button.transparent>
                        </div>
                    </header>

                    <div class="flex flex-wrap items-start aura-layout-row">
                        @foreach ($definition->fieldsList() as $blockField)
                            @php
                                $input = array_merge([
                                    'validation' => '',
                                    'style' => ['width' => '100'],
                                ], $blockField);
                                $input['slug'] = 'blocks.'.$index.'.data.'.$blockField['slug'];
                                $input['field'] = app($input['type']);
                            @endphp

                            <x-dynamic-component :component="$input['field']->edit()" :field="$input" :form="$form" />
                        @endforeach
                    </div>
                </section>
            @else
                <section class="aura-alert aura-alert-warning aura-radius-panel" style="margin-bottom: var(--aura-section-gap, 1rem)">
                    {{ __('The :type block type is no longer registered.', ['type' => $block['type'] ?? __('unknown')]) }}
                    <button type="button" class="ml-2 font-semibold underline" wire:click="removeBlock({{ $index }})">{{ __('Remove') }}</button>
                </section>
            @endif
        @empty
            <div class="border border-dashed text-center aura-group-section" data-aura-blocks-empty>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('No content blocks yet') }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Choose a block type below to start building the content.') }}</p>
            </div>
        @endforelse
    </div>

    @if ($definitions)
        <div x-data="{ type: '{{ array_key_first($definitions) }}' }" class="flex items-center gap-2">
            <div class="w-full min-w-0 md:w-72">
                <x-aura::input.select
                    :id="'aura-blocks-type-'.$this->getId()"
                    x-model="type"
                    :options="collect($definitions)->map(fn ($definition) => $definition->label())->all()"
                    :selected="array_key_first($definitions)"
                    aria-label="{{ __('Block type') }}"
                />
            </div>

            <x-aura::button.border type="button" x-on:click="$wire.addBlock(type)" class="shrink-0">
                <x-slot:icon><x-aura::icon icon="plus" size="xs" /></x-slot:icon>
                {{ __('Add block') }}
            </x-aura::button.border>
        </div>
    @else
        <p class="text-sm text-red-600 dark:text-red-400">{{ __('No valid block types are configured for this field.') }}</p>
    @endif

    @error('blockType')
        <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
