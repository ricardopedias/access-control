
@component('acl::document')

    @slot('title') Lixeira de Usuários @endslot

    @include('acl::breadcrumb')

    <hr>

    @acl_content('users.read')

        <div class="row">

            <div class="col d-md-flex justify-content-md-between">

                <div class="d-flex d-md-block justify-content-center">
                @sg_perpage
                </div>

                <div class="d-block mb-3 w-100 d-md-none"></div>

                <div class="d-flex d-md-block justify-content-center">

                    <div>
                    @sg_search
                    </div>

                </div>

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

                        @acl_action_sm('users.delete', route($route_restore, $item->id), 'Restaurar', 'acl::buttons.restore', true)

                        @acl_action_sm('users.delete', route($route_destroy, $item->id), 'Excluir', null, true)

                    </td>
                </tr>

            @endforeach

        @end_sg_table

        <div class="row">

            <div class="col text-center text-md-left mb-3">

                @sg_info

            </div>

            <div class="w-100 d-md-none"></div>

            <div class="col d-flex d-md-block justify-content-center">

                @sg_pagination

            </div>

        </div>

    @end_acl_content

@endcomponent
