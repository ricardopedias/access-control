
    @if ($status==false)

        <a href="javascript:void(0)" class="acl-submit btn btn-info disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
           title="Você não tem permissão para '{{ $label }}'">
            <i class="{{ $icon or 'fas fa-save' }}"></i>
            <span class="d-none d-lg-inline">{{ $label }}</span>
        </a>

        <script>

            // Neste momento o JQuery pode ainda não existir
            // por isso, executa ao terminar

            var acl_form_block = function(){

                // Desativa a submissão do formulário através da tecla 'Enter'
                $('.acl-submit').closest("form").submit(function(){
                    return false;
                });
            };

            if (undefined === window.$) {
                window.onload = acl_form_block;
            }
            else {
                $(document).ready(acl_form_block);
            }

        </script>

    @else

        <button type="submit" class="btn btn-info {{ $size != 'none' ? "btn-{$size}" : '' }}"
           title="{{ $label }}">
            <i class="{{ $icon or 'fas fa-save' }}"></i>
            <span class="d-none d-lg-inline">{{ $label }}</span>
        </button>

    @endif
