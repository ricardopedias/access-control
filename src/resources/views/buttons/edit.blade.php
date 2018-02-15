    <a href="{{ $url }}" class="btn btn-info {{ ($status==false ? 'disabled' : '') }} {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="{{ $label }}">
        <i class="fa fa-edit"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>
