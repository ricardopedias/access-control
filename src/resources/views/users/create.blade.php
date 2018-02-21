
@component('laracl::document')

    @slot('title') Novo Usuário @endslot

    @acl_content('users.show')

        <div class="row mb-3">

            <div class="col">

                @acl_action('users.show', route($route_index), 'Lista de Usuários')

            </div>

            <div class="col text-right justify-content-end">

                {{-- ... --}}

            </div>
            
        </div>

        <hr>

        <form method="post" action="{{ route($route_store) }}">

            {{ csrf_field() }}

            @include('laracl::users.form')

            <div class="row">

                <div class="col">

                    @acl_submit_lg('users.create', 'Novo Usuário')   

                </div>

            </div>

        </form>
        
    @end_acl_content

@endcomponent