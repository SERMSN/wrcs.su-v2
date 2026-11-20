@props(['skill'])

<li class="skill">
    <div class="skill__head">
        <span class="skill__name">{{ $skill['name'] }}</span>
        <span class="skill__tier tier tier--{{ $skill['tier'] }}">{{ __('ui.skills.tiers.'.$skill['tier']) }}</span>
    </div>
    <div class="skill__track" role="img" aria-label="{{ $skill['name'] }} — {{ $skill['level'] }}%">
        <span class="skill__fill" style="--level: {{ (int) $skill['level'] }}%"></span>
    </div>
</li>
