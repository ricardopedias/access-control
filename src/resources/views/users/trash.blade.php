
@component('laracl::document')

    @slot('title') Lixeira de Usuários @endslot

    @include('laracl::breadcrumb')

    <hr>

    @acl_content('users.read')

        <div class="row">

            <div class="col-2">

                @sg_perpage

            </div>

            <div class="col-10 text-right justify-content-end">

                @sg_search

            </div>

        </div>

        @sg_table

            @foreach($collection as $item)

                <tr>
                    <td class="text-center">{{ $item->id }}</td>

                    <td class="text-center">{{ $item->name }}</td>

                    <td class="text-center">
                        @if ($item->group_name)
                            Grupo {{ $item->group_name }}
                        @else
                            Exclusivas
                        @endif
                    </td>

                    <td class="text-center">{!! str_replace(['@', '.'], ['<wbr>@', '<wbr>.'], $item->email) !!}</td>

                    <td class="text-center">{{ $item->created_at->format('d/m/Y H:i:s') }}</td>

                    <td class="text-center">

                        @acl_action_sm('users.delete', route($route_restore, $item->id), 'Restaurar', 'laracl::buttons.restore', true)

                        @acl_action_sm('users.delete', route($route_destroy, $item->id), 'Excluir', null, true)

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
