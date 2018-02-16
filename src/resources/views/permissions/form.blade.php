        <form method="post" action="{{ route( config('laracl.routes.perms_update'), $user->id) }}">

            <div class="row mt-3">

                <div class="col">

                    {{ csrf_field() }}

                    {{ method_field('PUT') }} 
                    {{-- https://laravel.com/docs/5.5/controllers#resource-controllers --}}

                    <table class="table table-striped table-bordered">

                        <thead>

                            <th>Área da Loja</th>

                            <th>Criar</th>

                            <th>Editar</th>

                            <th>Ver</th>

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

                    <button type="submit" class="btn btn-lg btn-primary">
                        <i class="fa fa-save"></i>
                        Aplicar Permissões
                    </button>        

                </div>

            </div>

        </form>