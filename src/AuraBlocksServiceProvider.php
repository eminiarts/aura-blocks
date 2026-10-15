<?php

namespace Aura\Blocks;

use Aura\Blocks\Fields\Blocks;
use Aura\Blocks\Livewire\BlocksEditor;
use Aura\Blocks\Support\BlockDefinition;
use Aura\Blocks\Support\BlockEditor;
use Aura\Blocks\Support\BlockRegistry;
use Aura\Blocks\Support\BlockRenderer;
use Aura\Blocks\Support\BlockValidator;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AuraBlocksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('aura-blocks')
            ->hasConfigFile()
            ->hasViews('aura-blocks');
    }

    public function packageBooted(): void
    {
        $registry = $this->app->make(BlockRegistry::class);

        foreach ((array) config('aura-blocks.types', []) as $type => $definition) {
            if ($definition instanceof BlockDefinition) {
                $registry->register($definition);

                continue;
            }

            if (is_string($type) && is_array($definition)) {
                $registry->register($definition, $type);
            }
        }

        $this->app->make('aura')::registerFields([Blocks::class]);
        Livewire::component('aura-blocks::editor', BlocksEditor::class);
        Livewire::resolveMissingComponent(
            fn (string $name): ?string => $name === 'aura-blocks::editor' ? BlocksEditor::class : null,
        );
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(BlockRegistry::class);
        $this->app->singleton(BlockEditor::class);
        $this->app->singleton(BlockRenderer::class);
        $this->app->singleton(BlockValidator::class);
    }
}
