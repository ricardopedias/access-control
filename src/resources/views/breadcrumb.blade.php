<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white d-flex justify-content-center justify-content-md-start">
        @foreach($breadcrumb as $label => $link)
            @if(empty($link) || is_numeric($label))
                <li class="breadcrumb-item active" aria-current="page">
                    @if(is_numeric($label))
                    {!! $link !!}
                    @else
                    {!! $label !!}
                    @endif
                </li>
            @else
                <li class="breadcrumb-item"><a href="{{ $link }}">{!! $label !!}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
