
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    <hr>

    @acl_content('users.show')

        <div class="row">

            <div class="col">

                @sg_perpage

            </div>

            <div class="col text-right justify-content-end">

                @acl_action('users.create', route($route_create), 'Novo Usuário')

                @acl_action('groups.show', route($route_groups), 'Grupos de Acesso')

                @sg_search

            </div>
            
        </div>

        @sg_table

            @foreach($collection as $item)

                <tr>
                    <td class="text-center">{{ $item->id }}</td>

                    <td>{{ $item->name }}</td>

                    <td>{{ $item->group_name }}</td>

                    <td>{!! str_replace(['@', '.'], ['<wbr>@', '<wbr>.'], $item->email) !!}</td>

                    <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>

                    <td class="text-center">
                        
                        @acl_action_sm('users.edit', route($route_edit, $item->id ), 'Editar')

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