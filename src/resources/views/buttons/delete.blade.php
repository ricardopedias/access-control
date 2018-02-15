    <a href="{{ $url }}" class="btn btn-danger {{ ($status==false ? 'disabled' : '') }} {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="{{ $label }}">
        <i class="fa fa-times"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>
