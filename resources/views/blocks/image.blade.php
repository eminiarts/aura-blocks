<figure class="aura-block aura-block-image">
    <img src="{{ $block['url'] ?? '' }}" alt="{{ $block['alt'] ?? '' }}">

    @if (! empty($block['caption']))
        <figcaption>{{ $block['caption'] }}</figcaption>
    @endif
</figure>
