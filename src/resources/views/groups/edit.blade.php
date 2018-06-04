
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    <hr>

    @acl_content('groups.read')

        <div class="row mb-3">

            <div class="col">

                @acl_action('groups.read', route($route_index), 'Lista de Grupos',  'laracl::buttons.groups.read')

            </div>

            <div class="col text-right justify-content-end">

                @acl_action('groups.create', route($route_create), 'Novo Grupo',  'laracl::buttons.groups.create')

                @acl_action('groups-permissions.update', route($route_permissions, $model->id), 'Permissões',  'laracl::buttons.groups.permissions')

            </div>

        </div>

        <form method="post" action="{{ route($route_update, $model->id) }}">

            {{ csrf_field() }}

            {{ method_field('PUT') }}
            {{-- https://laravel.com/docs/5.5/controllers#resource-controllers --}}

            @include('laracl::groups.form')

            <div class="row">

                <div class="col">

                    @acl_submit_lg('groups.update', 'Atualizar Grupo')

                </div>

            </div>

        </form>

    @end_acl_content

@endcomponent
