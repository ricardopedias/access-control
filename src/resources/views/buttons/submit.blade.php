    
    @if ($status==false)

        <a href="javascript:void(0)" class="acl-submit btn btn-info disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
           title="{{ $label }}">
            <i class="fa fa-save"></i>
            <span class="d-none d-lg-inline">{{ $label }}</span>
        </a>    

        <script>

            // Neste momento o JQuery pode ainda não existir
            // por isso, executa ao terminar
            window.onload = function(){
                
                // Desativa a submissão do formulário através da tecla 'Enter'
                $('.acl-submit').closest("form").submit(function(){
                    return false;
                });
            };

        </script>

    @else
    
        <button type="submit" class="btn btn-info {{ $size != 'none' ? "btn-{$size}" : '' }}"
           title="{{ $label }}">
            <i class="fa fa-save"></i>
            <span class="d-none d-lg-inline">{{ $label }}</span>
        </button>

    @endif
