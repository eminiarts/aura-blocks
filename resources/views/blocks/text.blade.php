<section class="aura-block aura-block-text">
    @if (! empty($block['heading']))
        <h2>{{ $block['heading'] }}</h2>
    @endif

    @if (! empty($block['text']))
        <div>{!! nl2br(e($block['text'])) !!}</div>
    @endif
</section>
