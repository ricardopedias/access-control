    <a href="{{ $url }}" class="btn btn-secondary {{ ($status==false ? 'disabled' : '') }} {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="{{ $label }}">
        <i class="fa fa-search"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>
