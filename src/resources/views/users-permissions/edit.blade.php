
@component('laracl::document')

    @slot('title') {{ $title }} @endslot

    <hr>

    @acl_content('users-permissions.show')

        <div class="row mb-3">

            <div class="col">

                @acl_action('users.show', route($route_index), 'Lista de Usuários')

            </div>

            <div class="col text-right">

                @acl_action('users.edit', route($route_user, $user->id), 'Editar Usuário')

                @acl_action('groups.show', route($route_groups), 'Grupos de Acesso')

            </div>

        </div>

        @if($has_permissions == false)

            <div class="row">

                <div class="col">

                    <div class="alert alert-info">

                        <h4 class="alert-heading">Atenção, este usuário pertence ao grupo <strong>"{{ optional($user->group)->name }}"</strong>!</h4>

                        <p>Atualmente o usuário possui as permissões do grupo. Clicando em <i>"Aplicar Permissões"</i>, este usuário possuirá <strong>Privilégios Personalizados</strong>. Você poderá remover estes privilégios personalizados a qualquer momento na tela de edição do usuário, bastando setar um grupo para ele.
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

                            <th>Áreas de Acesso</th>

                            <th class="text-center" style="background:rgba(0,0,0,0.05)"><i class="fa fa-eye"></i> Ver</th>

                            <th class="text-center"><i class="fa fa-plus-circle"></i> Criar</th>

                            <th class="text-center" style="background:rgba(0,0,0,0.05)"><i class="fa fa-edit"></i> Editar</th>

                            <th class="text-center"><i class="fa fa-times-circle"></i> Excluir</th>

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

                                        @if($loop->iteration%2 == 0)
                                        <td class="text-center">
                                        @else
                                        <td class="text-center" style="background:rgba(0,0,0,0.05)">
                                        @endif

                                            @if($role_value != null)

                                                <input type="checkbox" name="{{ $role_name }}" class="check-toggle"
                                                       data-on-text="Sim" data-off-text="Não"
                                                       value="yes" {{ old_check($role_name, 'yes', $role_value) }}>

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

    @end_acl_content

@endcomponent
