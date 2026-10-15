<?php

namespace Aura\Blocks\Livewire;

use Aura\Blocks\Support\BlockEditor;
use Aura\Blocks\Support\BlockRegistry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class BlocksEditor extends Component
{
    /** @var array<string, mixed> */
    public array $field = [];

    /** @var array{fields: array{blocks: array<int, array<string, mixed>>}} */
    public array $form = [
        'fields' => [
            'blocks' => [],
        ],
    ];

    #[Modelable]
    public ?array $value = [];

    public function addBlock(string $type): void
    {
        $definition = app(BlockRegistry::class)->forField($this->field)[$type] ?? null;

        if (! $definition) {
            $this->addError('blockType', __('This block type is not available.'));

            return;
        }

        $this->setBlocks(app(BlockEditor::class)->add($this->blocks(), $definition));
    }

    public function mount(): void
    {
        $this->setBlocks(is_array($this->value) ? $this->value : []);
    }

    public function moveBlockDown(int $index): void
    {
        $this->setBlocks(app(BlockEditor::class)->moveDown($this->blocks(), $index));
    }

    public function moveBlockUp(int $index): void
    {
        $this->setBlocks(app(BlockEditor::class)->moveUp($this->blocks(), $index));
    }

    public function removeBlock(int $index): void
    {
        $this->setBlocks(app(BlockEditor::class)->remove($this->blocks(), $index));
    }

    public function render(): View
    {
        return view('aura-blocks::livewire.blocks-editor', [
            'blocks' => $this->blocks(),
            'definitions' => app(BlockRegistry::class)->forField($this->field),
        ]);
    }

    public function updatedForm(): void
    {
        $this->value = $this->blocks();
    }

    /** @return array<int, array<string, mixed>> */
    private function blocks(): array
    {
        $blocks = $this->form['fields']['blocks'] ?? [];

        return is_array($blocks) ? array_values($blocks) : [];
    }

    /** @param array<int, array<string, mixed>> $blocks */
    private function setBlocks(array $blocks): void
    {
        $this->form['fields']['blocks'] = array_values($blocks);
        $this->value = array_values($blocks);
    }
}
