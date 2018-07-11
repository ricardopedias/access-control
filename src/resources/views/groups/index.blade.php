
@component('acl::document')

    @slot('title') Grupos de Acesso @endslot

    @include('acl::breadcrumb')

    <hr>

    @acl_content('groups.read')

        <div class="row justify-content-end align-items-start pl-3 pr-3">

            <div class="mr-auto">
                @sg_perpage
            </div>

            <div>
                @acl_action('groups.create', route($route_create), 'Novo Grupo', 'acl::buttons.groups.create')

                @sg_search

                @if(config('acl.soft_delete') != false)
                    @acl_action('groups.delete', route($route_trash), '', 'acl::buttons.trash')
                @endif
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

                        @acl_action_sm('groups-permissions.update', route($route_permissions, $item->id ), 'Permissões', 'acl::buttons.permissions')

                        @acl_action_sm('groups.delete', route($route_destroy, $item->id), 'Excluir', null, true)

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
