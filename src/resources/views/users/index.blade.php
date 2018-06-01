
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    <hr>

    @acl_content('users.read')

        <div class="row">

            <div class="col-2">

                @sg_perpage

            </div>

            <div class="col-10 text-right justify-content-end">

                @acl_action('users.create', route($route_create), 'Novo Usuário')

                @acl_action('groups.read', route($route_groups), 'Grupos de Acesso')

                @sg_search

            </div>

        </div>

        @sg_table

            @foreach($collection as $item)

                <tr>
                    <td class="text-center">{{ $item->id }}</td>

                    <td>{{ $item->name }}</td>

                    <td>
                        @if ($item->group_name)
                            Grupo {{ $item->group_name }}
                        @else
                            Exclusivas
                        @endif
                    </td>

                    <td>{!! str_replace(['@', '.'], ['<wbr>@', '<wbr>.'], $item->email) !!}</td>

                    <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>

                    <td class="text-center">

                        @acl_action_sm('users.update', route($route_edit, $item->id ), 'Editar')

                        @acl_action_sm('users-permissions.update', route($route_permissions, $item->id), 'Permissões')

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
