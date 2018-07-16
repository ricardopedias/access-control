
@component('acl::document')

    @slot('title') {{ $title }} @endslot

    @include('acl::breadcrumb')

    <hr>

    @acl_content('users.read')

        <div class="row mb-3">

            <div class="col text-right justify-content-end">

                @acl_action('users.create', route($route_create), 'Novo Usuário', 'acl::buttons.users.create')

                @acl_action('users-permissions.update', route($route_permissions, $model->id), 'Permissões', 'acl::buttons.users.permissions')

            </div>

        </div>

        <form method="post" action="{{ route($route_update, $model->id) }}">

            {{ csrf_field() }}

            {{ method_field('PUT') }}
            {{-- https://laravel.com/docs/5.5/controllers#resource-controllers --}}

            @include('acl::users.form')

            <div class="row">

                <div class="col d-flex justify-content-center justify-content-sm-end">

                    @acl_submit_lg('users.update', 'Atualizar Usuário')

                </div>

            </div>

        </form>

    @end_acl_content

@endcomponent
