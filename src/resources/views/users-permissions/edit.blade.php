
@component('laracl::document')

    @slot('title')  Permissoes Específicas para "{{ $user->name }}" @endslot

    @aclock('users-permissions.show')

        <div class="row mb-3">

            <div class="col">

                @acl_action('users.show', route($route_index), 'Lista de Usuários')

            </div>

            <div class="col text-right justify-content-end">

                @acl_action('users.edit', route($route_user, $user->id), 'Editar Usuário')

                @acl_action('groups.show', route($route_groups), 'Grupos de Acesso')

            </div>
            
        </div>

        @if($has_permissions == false)

            <div class="row">

                <div class="col">

                    <div class="alert alert-info">
                        
                        <h4 class="alert-heading">Atenção!</h4>

                        <p>Este usuário possui os privilégios do grupo de acesso <strong>"{{ $user->group->name }}"</strong>. Clicando em "Aplicar Permissões", este usuário possuirá privilégios exclusivos. Isso pode ser mudado posteriormente setando um novo grupo para ele.
                        </p>

                    </div>

                </div>

            </div>

        @endif

        <form method="post" action="{{ route($route_update, $user->id) }}">

            <div class="row">

                <div class="col">

                    {{ csrf_field() }}

                    {{ method_field('PUT') }} 
                    {{-- https://laravel.com/docs/5.5/controllers#resource-controllers --}}

                    <table class="table table-striped table-bordered">

                        <thead>

                            <th>Área da Loja</th>
                            
                            <th>Ver</th>

                            <th>Criar</th>

                            <th>Editar</th>

                            <th>Excluir</th>

                        </thead>

                        <tbody>

                            @foreach($roles as $route => $item)

                                <tr>
                                    <td>
                                        {{ $item['label'] }}

                                        {{-- É necessário para que a função sempre exista na matriz, 
                                        mesmo quando não existirem permissões ativas --}}
                                        <input type="hidden" name="roles[{{ $route }}]['exists']" value="1">
                                    </td>

                                    @foreach($item['roles'] as $role => $role_value)

                                        @php
                                        $role_name = "roles[{$route}][{$role}]";
                                        @endphp

                                        <td>
                                            @if($role_value != null)

                                                <input type="checkbox" name="{{ $role_name }}" class="check-toggle" 
                                                       data-on-text="Sim" data-off-text="Não"
                                                       value="yes" {{ old_check($role_name, $role_value, 'yes') }}>

                                            @endif

                                        </td>

                                    @endforeach
                                    
                                </tr>

                            @endforeach
                            
                        </tbody>
                    </table>

                </div>

            </div>

            <div class="row">

                <div class="col text-right">

                    @acl_submit_lg('users-permissions.edit', 'Aplicar Permissões')

                </div>

            </div>

        </form>

    @endaclock

@endcomponent