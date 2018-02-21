
@component('laracl::document')

    @slot('title') Usuários @endslot

    @acl_content('users.show')

        <div class="row mb-3">

            <div class="col">

                {{-- ... --}}

            </div>

            <div class="col text-right justify-content-end">

                @acl_action('users.create', route($route_create), 'Novo Usuário')

                @acl_action('groups.show', route($route_groups), 'Grupos de Acesso')

            </div>
            
        </div>

        <div class="table-responsive">

            <table class="table table-striped table-bordered table-header">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Criação</th>
                        <th></th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($collection as $item)

                        <tr>
                            <td class="text-center">{{ $item->id }}</td>

                            <td>{{ $item->name }}</td>

                            <td>{!! str_replace(['@', '.'], ['<wbr>@', '<wbr>.'], $item->email) !!}</td>

                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>

                            <td class="text-center">
                                
                                @acl_action_sm('users.edit', route($route_edit, $item->id ), 'Editar')

                            </td>
                        </tr>

                    @endforeach
                    
                </tbody>
            </table>

        </div>

        <div class="row">

            <div class="col">

                <button class="btn text-primary" style="background: transparent; box-shadow: none !important;">

                    <i class="fa fa-info-circle"></i>

                    Exibindo de {{ $collection->firstItem() }} a {{ $collection->lastItem() }} 

                    @if($collection->total() == 1)
                        de {{ $collection->total() }} registro
                    @else
                        de {{ $collection->total() }}  registros
                    @endif

                    @if($collection->lastPage() == 1)
                        em uma página
                    @else
                        em {{ $collection->lastPage() }} páginas
                    @endif

                </button>

                {{-- Redireciona se o numero de paginas não for válido --}}
                @if($collection->currentPage() > $collection->lastPage())

                    @php 
                        $qstring = array_merge(request()->all(), ['page' => $collection->lastPage()]); 
                    @endphp

                    <script>
                        $(location).attr({ href : '{!! route(request()->route()->getName(), $qstring) !!}' });
                    </script>

                @endif

            </div>

            <div class="col">
                {{ $collection->links('laracl::table-pagination') }}
            </div>
            
        </div>

    @end_acl_content

@endcomponent