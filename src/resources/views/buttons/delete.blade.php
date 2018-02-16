   
@if ($status==false)

    <a href="{{ $url }}" class="btn btn-danger disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="Você não tem permissão para '{{ $label }}'">
        <i class="fa fa-times"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@else

    <a href="{{ $url }}" class="btn btn-danger {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="{{ $label }}">
        <i class="fa fa-times"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@endif
