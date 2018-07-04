
@component('laracl::document')

    @slot('title') Lixeira de Grupos de Acesso @endslot

    @include('laracl::breadcrumb')

    <hr>

    @acl_content('groups.read')

        <div class="row justify-content-end align-items-start pl-3 pr-3">

            <div class="mr-auto">
                @sg_perpage
            </div>

            <div>
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

                        @acl_action_sm('groups.delete', route($route_restore, $item->id), 'Restaurar', 'laracl::buttons.restore', true)

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
