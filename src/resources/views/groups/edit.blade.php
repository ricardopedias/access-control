
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    <hr>

    @acl_content('groups.show')

        <div class="row mb-3">

            <div class="col">

                @acl_action('groups.show', route($route_index), 'Lista de Grupos')

            </div>

            <div class="col text-right justify-content-end">

                @acl_action('groups.create', route($route_create), 'Novo Grupo')

                @acl_action('groups-permissions.edit', route($route_permissions, $model->id), 'Permissões')

            </div>
            
        </div>

        <form method="post" action="{{ route($route_update, $model->id) }}">

            {{ csrf_field() }}

            {{ method_field('PUT') }} 
            {{-- https://laravel.com/docs/5.5/controllers#resource-controllers --}}

            @include('laracl::groups.form')

            <div class="row">

                <div class="col">

                    @acl_submit_lg('groups.edit', 'Atualizar Grupo')   

                </div>

            </div>

        </form>

    @end_acl_content

@endcomponent