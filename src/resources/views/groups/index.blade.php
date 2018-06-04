
@component('laracl::document')

    @slot('title') Grupos de Acesso @endslot

    <hr>

    @acl_content('groups.read')

        <div class="row">

            <div class="col">

                @sg_perpage

                @acl_action('users.read', route($route_users), 'Usuários', 'laracl::buttons.users.read')


            </div>

            <div class="col text-right">

                @acl_action('groups.create', route($route_create), 'Novo Grupo', 'laracl::buttons.groups.create')

                @sg_search

            </div>

        </div>

        @sg_table

            @foreach($collection as $item)

                <tr>
                    <td class="text-center">{{ $item->id }}</td>

                    <td>
                        {{ $item->name }}
                        @if($item->system == 'yes')
                        <small>(Sistema)</small>
                        @endif
                    </td>

                    <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>

                    <td class="text-center">

                        @acl_action_sm('groups.update', route($route_edit, $item->id ), 'Editar')

                        @acl_action_sm('groups-permissions.update', route($route_permissions, $item->id ), 'Permissões', 'laracl::buttons.permissions')

                        @acl_action_sm('groups.delete', route($route_destroy, $item->id), 'Excluir')

                    </td>
                </tr>

            @endforeach

        @end_sg_table

        <div class="row">

            <div class="col">

                @sg_info

            </div>

            <div class="col">

                @sg_pagination

            </div>

        </div>

    @end_acl_content

@endcomponent
