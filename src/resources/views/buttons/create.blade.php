
@if ($status==false)

    <a href="{{ $url }}" class="btn btn-success disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="Você não tem permissão para '{{ $label }}'">
        <i class="{{ $icon or 'fas fa-plus' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@else

    <a href="{{ $url }}" class="btn btn-success {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="{{ $label }}">
        <i class="{{ $icon or 'fa fa-plus' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@endif
