@props(['overline' => null, 'headline', 'subtitle' => null, 'center' => false, 'actionText' => null, 'actionHref' => null])

<div class="{{ $center ? 'text-center max-w-2xl mx-auto' : 'flex flex-col md:flex-row md:items-end justify-between gap-6' }}">
  <div class="{{ $center ? '' : 'max-w-xl' }}">
    @if($overline)
      <p class="gico-overline text-sienna-600">{{ $overline }}</p>
    @endif
    <h2 class="{{ $overline ? 'mt-4' : '' }} font-display text-display-lg text-chocolate-900">{!! $headline !!}</h2>
    @if($subtitle)
      <p class="mt-4 text-lg text-chocolate-800/80">{{ $subtitle }}</p>
    @endif
  </div>
  @if($actionText && $actionHref && !$center)
    <a href="{{ $actionHref }}" class="self-start md:self-end inline-flex items-center gap-2 text-sm font-medium text-chocolate-900 border-b border-chocolate-900 pb-1 hover:text-sienna-600 hover:border-sienna-600 transition">
      {{ $actionText }} <span aria-hidden="true">→</span>
    </a>
  @endif
</div>
