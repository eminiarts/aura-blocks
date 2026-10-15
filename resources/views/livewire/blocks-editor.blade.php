<div class="space-y-4" data-aura-blocks-editor>
    <div class="space-y-3">
        @forelse ($blocks as $index => $block)
            @php
                $definition = $definitions[$block['type'] ?? ''] ?? null;
                $blockId = $block['id'] ?? $index;
            @endphp

            @if ($definition)
                <section
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
                    wire:key="aura-block-{{ $blockId }}"
                    data-block-type="{{ $definition->type() }}"
                >
                    <header class="flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-100 text-xs font-semibold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
                                {{ $index + 1 }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $definition->label() }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $definition->type() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-white hover:text-gray-700 disabled:cursor-not-allowed disabled:opacity-30 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                wire:click="moveBlockUp({{ $index }})"
                                @disabled($index === 0)
                                aria-label="{{ __('Move block up') }}"
                            >
                                <x-aura::icon icon="chevron-up" size="xs" />
                            </button>
                            <button
                                type="button"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-white hover:text-gray-700 disabled:cursor-not-allowed disabled:opacity-30 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                wire:click="moveBlockDown({{ $index }})"
                                @disabled($index === count($blocks) - 1)
                                aria-label="{{ __('Move block down') }}"
                            >
                                <x-aura::icon icon="chevron-down" size="xs" />
                            </button>
                            <button
                                type="button"
                                class="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400"
                                wire:click="removeBlock({{ $index }})"
                                wire:confirm="{{ __('Remove this block?') }}"
                                aria-label="{{ __('Remove block') }}"
                            >
                                <x-aura::icon icon="trash" size="xs" />
                            </button>
                        </div>
                    </header>

                    <div class="flex flex-wrap py-4">
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
                <section class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200">
                    {{ __('The :type block type is no longer registered.', ['type' => $block['type'] ?? __('unknown')]) }}
                    <button type="button" class="ml-2 font-semibold underline" wire:click="removeBlock({{ $index }})">{{ __('Remove') }}</button>
                </section>
            @endif
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-5 py-8 text-center dark:border-gray-700 dark:bg-gray-900/50">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('No content blocks yet') }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Choose a block type below to start building the content.') }}</p>
            </div>
        @endforelse
    </div>

    @if ($definitions)
        <div x-data="{ type: '{{ array_key_first($definitions) }}' }" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <select
                x-model="type"
                class="block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 sm:max-w-xs"
                aria-label="{{ __('Block type') }}"
            >
                @foreach ($definitions as $definition)
                    <option value="{{ $definition->type() }}">{{ $definition->label() }}</option>
                @endforeach
            </select>

            <x-aura::button.border type="button" x-on:click="$wire.addBlock(type)" class="justify-center">
                <span class="inline-flex items-center gap-2">
                    <x-aura::icon icon="plus" size="xs" />
                    {{ __('Add block') }}
                </span>
            </x-aura::button.border>
        </div>
    @else
        <p class="text-sm text-red-600 dark:text-red-400">{{ __('No valid block types are configured for this field.') }}</p>
    @endif

    @error('blockType')
        <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
