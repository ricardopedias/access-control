
@if ($status==false)

    <a href="{{ $url }}" class="btn btn-info disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="Você não tem permissão para '{{ $label }}'">
        <i class="{{ $icon or 'fas fa-edit' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@else

    <a href="{{ $url }}" class="btn btn-info {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="{{ $label }}">
        <i class="{{ $icon or 'fas fa-edit' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@endif
