
@component('laracl::document')

    @slot('title') Novo Grupo de Acesso @endslot

    @acl_content('groups.show')

        <div class="row mb-3">

            <div class="col">

                @acl_action('groups.show', route($route_index), 'Lista de Grupos')
                @acl_action('users.show', route($route_users), 'Lista de Usuários')

            </div>

            <div class="col text-right justify-content-end">

                {{-- ... --}}

            </div>
            
        </div>

        <hr>

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