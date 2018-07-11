{{-- @php

    $data = [ 
        'component' => $component,
        'title'     => $title,
        'user'      => $user,
        'roles'     => $roles,
    ];

@endphp --}}

@component($component)

    {{-- @php
        extract($data);
    @endphp --}}

    @include('acl::permissions.form')

@endcomponent