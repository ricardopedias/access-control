
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    <hr>

    @acl_content('groups.read')

        <div class="row mb-3">

            <div class="col">

                @acl_action('groups.read', route($route_index), 'Lista de Grupos')
                @acl_action('users.read', route($route_users), 'Lista de Usuários')

            </div>

            <div class="col text-right justify-content-end">

                {{-- ... --}}

            </div>

        </div>

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
