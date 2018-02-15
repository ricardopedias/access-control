    <a href="{{ $url }}" class="btn btn-success {{ ($status==false ? 'disabled' : '') }} {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="{{ $label }}">
        <i class="fa fa-plus"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>
