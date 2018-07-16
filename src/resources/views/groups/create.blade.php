
@component('acl::document')

    @slot('title') {{ $title }} @endslot

    @include('acl::breadcrumb')

    <hr>

    @acl_content('groups.read')

        <form method="post" action="{{ route($route_store) }}">

            {{ csrf_field() }}

            @include('acl::groups.form')

            <div class="row">

                <div class="col d-flex justify-content-center justify-content-sm-end">

                    @acl_submit_lg('groups.create', 'Novo Grupo')

                </div>

            </div>

        </form>

    @end_acl_content

@endcomponent
