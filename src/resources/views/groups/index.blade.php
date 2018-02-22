
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    <hr>

    @acl_content('groups.show')

        <div class="row">

            <div class="col">

                @sg_perpage

                @acl_action('users.show', route($route_users), 'Usuários')


            </div>

            <div class="col text-right">

                @acl_action('groups.create', route($route_create), 'Novo Grupo')

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
                        
                        @acl_action_sm('groups.edit', route($route_edit, $item->id ), 'Editar')

                        @acl_action_sm('groups-permissions.edit', route($route_permissions, $item->id ), 'Permissões')

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