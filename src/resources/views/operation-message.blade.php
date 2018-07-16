@if (session()->has('success'))

<div class="alert alert-success">
    <i class="fa fa-check"></i> {{ session()->pull('success') }} <br>
</div>

@elseif (session()->has('error'))

    <div class="alert alert-error">
        <i class="fa fa-times"></i> {{ session()->pull('error') }} <br>
    </div>

@elseif ($errors->any())

    <div class="alert alert-warning">

        @foreach ($errors->all() as $error)
            <i class="fa fa-angle-right"></i> {{ $error }} <br>
        @endforeach

    </div>

@endif
