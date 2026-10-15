@php
    $slug = $field['slug'];
    $configuration = collect($field)->except(['field'])->all();
    $editorKey = 'aura-blocks-'.$slug.'-'.(optional($this->model ?? null)->getKey() ?? 'new');
@endphp

<x-aura::fields.wrapper :field="$field">
    <livewire:aura-blocks::editor
        :field="$configuration"
        wire:model="form.fields.{{ $slug }}"
        :key="$editorKey"
    />
</x-aura::fields.wrapper>
