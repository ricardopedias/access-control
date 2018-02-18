
@component('laracl::document')

    @slot('title') Editar Usuário @endslot

    @aclock('users.show')

        <div class="row mb-3">

            <div class="col">

                @acl_action('users.show', route($route_index), 'Lista de Usuários')

            </div>

            <div class="col text-right justify-content-end">

                @acl_action('users.create', route($route_create), 'Novo Usuário')

                @acl_action('users-permissions.edit', route($route_permissions, $model->id), 'Permissões')

            </div>
            
        </div>

        <hr>

        <form method="post" action="{{ route($route_update, $model->id) }}">

            {{ csrf_field() }}

            {{ method_field('PUT') }} 
            {{-- https://laravel.com/docs/5.5/controllers#resource-controllers --}}

            @include('laracl::users.form')

            <div class="row">

                <div class="col">

                    @acl_submit_lg('users.edit', 'Atualizar Usuário')

                </div>

            </div>

        </form>

    @endaclock

@endcomponent