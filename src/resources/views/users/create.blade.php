
@component('acl::document')

    @slot('title') {{ $title }} @endslot

    @include('acl::breadcrumb')

    <hr>

    @acl_content('users.read')

        <form method="post" action="{{ route($route_store) }}">

            {{ csrf_field() }}

            @include('acl::users.form')

            <div class="row">

                <div class="col">

                    @acl_submit_lg('users.create', 'Novo Usuário')

                </div>

            </div>

        </form>

    @end_acl_content

@endcomponent
