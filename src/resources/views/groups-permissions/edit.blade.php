
@component('acl::document')

    @slot('title') Permissões para "{{ $group->name }}" @endslot

    @include('acl::breadcrumb')

    <hr>

    @acl_content('groups-permissions.read')

        <div class="row mb-3">

            <div class="col text-right">

                @acl_action('groups.create', route($route_create), 'Novo Grupo',  'acl::buttons.groups.create')

            </div>

        </div>

        @include('acl::operation-message')

        <form method="post" action="{{ route($route_update, $group->id) }}">

            <div class="row mt-3">

                <div class="col">

                    {{ csrf_field() }}

                    {{ method_field('PUT') }}
                    {{-- https://laravel.com/docs/5.5/controllers#resource-controllers --}}

                    <table class="table table-striped table-bordered">

                        <thead>

                            <th>Áreas de Acesso</th>

                            <th class="text-center" title="Clique para selecionar"
                                style="cursor: pointer;"
                                onclick="checkUncheck('check-create')">
                                <i class="fa fa-plus-circle"></i>
                                <span class="d-none d-md-inline">Criar</span>
                            </th>

                            <th class="text-center" title="Clique para selecionar"
                                style="background:rgba(0,0,0,0.05); cursor: pointer;"
                                onclick="checkUncheck('check-read')">
                                <i class="fa fa-eye"></i>
                                <span class="d-none d-md-inline">Ver</span>
                            </th>

                            <th class="text-center" title="Clique para selecionar"
                                style="background:rgba(0,0,0,0.05); cursor: pointer;"
                                onclick="checkUncheck('check-update')">
                                <i class="fa fa-edit"></i>
                                <span class="d-none d-md-inline">Editar</span>
                            </th>

                            <th class="text-center" title="Clique para selecionar"
                                style="cursor: pointer;"
                                onclick="checkUncheck('check-delete')">
                                <i class="fa fa-times-circle"></i>
                                <span class="d-none d-md-inline">Excluir</span>
                            </th>

                        </thead>

                        <tbody>

                            @foreach($structure as $role => $item)

                                <tr>
                                    <td>
                                        {{ $item['label'] }}

                                        {{-- É necessário para que a função de acesso sempre exista na matriz,
                                        mesmo quando não existirem permissões ativas --}}
                                        <input type="hidden" name="permissions[{{ $role }}]['exists']" value="1">
                                    </td>

                                    @foreach($item['permissions'] as $perm => $perm_value)

                                        @php
                                        $perm_name = "permissions[{$role}][{$perm}]";
                                        @endphp

                                        @if($loop->iteration%2 == 0)
                                        <td class="text-center align-middle">
                                        @else
                                        <td class="text-center align-middle" style="background:rgba(0,0,0,0.05)">
                                        @endif

                                            @if($perm_value != null)

                                                <input type="checkbox" name="{{ $perm_name }}" class="check-toggle check-{{ $perm }}"
                                                       data-on-text="Sim" data-off-text="Não"
                                                       value="yes" {{ old_check($perm_name, 'yes', $perm_value) }}>

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

                <div class="col d-flex justify-content-center justify-content-sm-end">

                    @acl_submit_lg('groups-permissions.update', 'Aplicar Permissões')

                </div>

            </div>

        </form>

        <script>
            function checkUncheck(perm)
            {
                var all_checked = true;
                $('.' + perm).each(function() {
                    if ($(this).prop("checked") == false) {
                        all_checked = false;
                    }
                });

                $('.' + perm).each(function() {

                    if(all_checked==true) {
                        $(this).prop("checked", false);
                    } else {
                        $(this).prop("checked", true);
                    }
                });
            }
        </script>

    @end_acl_content

@endcomponent
