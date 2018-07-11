
@if ($status==false)

    <a href="{{ $url }}" class="btn btn-secondary disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="Você não tem permissão para '{{ $label }}'">
        <i class="{{ $icon or 'fas fa-retweet' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@else

    @php
        $btn_id = 'btn-restore-' . md5(microtime());
    @endphp

    <a id="{{ $btn_id }}" href="javascript:void(0)"
       class="btn btn-secondary {{ $size != 'none' ? "btn-{$size}" : '' }} acl-action-restore"
       data-url="{{ $url }}" title="{{ $label }}">
        <i class="{{ $icon or 'fas fa-retweet' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

    {{--
    O Código abaixo será renderizado apenas uma vez,
    quando o primeiro botão do tipo restore for invocado.
    --}}
    @php
        global $acl_restore_confirm_modal;

        if(isset($acl_restore_confirm_modal)) {
            $acl_restore_confirm_modal = false;
        } else {
            $acl_restore_confirm_modal = true;
        }
    @endphp

    @if($acl_restore_confirm_modal == true)

        <div id="js-acl-restore-confirm-modal-logic">

            @include('acl::modal-restore')

            <script>

                /*
                Ao final do carregamento do documento:
                1. Move a logica do modal de confirmação para o final do documento
                2. Gera uma instancia do objeto AclConfirmDelete e aplica o evento
                   de clique em todos os botões 'delete'.
                   O AclConfirmDelete é carregado em acl/src/resources/views/modal-delete.blade.php
                */

                function acl_attach_restore_confirm(elem) {
                    var confirm = new AclConfirmRestore();
                    confirm.debugMode({{ var_export(env('APP_DEBUG') || env('APP_ENV') === 'local') }});
                    confirm.setToken('{{ csrf_token() }}');
                    confirm.removeGridRow({{ $delete_row }});
                    confirm.attach(elem);
                }

                if (undefined === window.$) {

                    // Se jQuery ainda não estiver carregado

                    if(undefined === window.ready_acl_functions) {
                        window.ready_acl_functions = [];
                    }

                    window.ready_acl_functions.push(function(){
                        $('#js-acl-restore-confirm-modal-logic').appendTo("body");
                        $('.acl-action-restore').each(function(){
                            acl_attach_restore_confirm(this);
                        });
                    });

                    window.onload = function(){
                        $.each(window.ready_acl_functions, function(k, callback){
                            callback();
                        })
                    };

                } else {
                    // se jQuery já estiver disponível
                    $(document).ready(function(){
                        $('#js-acl-restore-confirm-modal-logic').appendTo("body");
                        $('.acl-action-restore').each(function(){
                            acl_attach_restore_confirm(this);
                        });
                    });
                }

            </script>
        </div>
    @endif

@endif
