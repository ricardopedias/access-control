
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    @include('laracl::breadcrumb')

    <hr>

    @acl_content('groups.read')

        <form method="post" action="{{ route($route_store) }}">

            {{ csrf_field() }}

            @include('laracl::groups.form')

            <div class="row">

                <div class="col">

                    @acl_submit_lg('groups.create', 'Novo Grupo')

                </div>

            </div>

        </form>

    @end_acl_content

@endcomponent
