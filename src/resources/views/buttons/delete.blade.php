
@if ($status==false)

    <a href="{{ $url }}" class="btn btn-danger disabled {{ $size != 'none' ? "btn-{$size}" : '' }}"
       title="Você não tem permissão para '{{ $label }}'">
        <i class="{{ $icon or 'fas fa-times' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

@else

    @php
        $btn_id = 'delete_' . md5($url);
        if (!isset($btns)) {
            $btns = [];
        }
        if (!isset($btns[$btn_id])) {
            $btns[$btn_id] = 1;
            $create_modal = true;
        } else {
            $create_modal = false;
        }

    @endphp

    <a href="{{ $url }}" class="btn btn-danger {{ $size != 'none' ? "btn-{$size}" : '' }}"
       data-toggle="modal" data-target="#{{ $btn_id }}"
       title="{{ $label }}">
        <i class="{{ $icon or 'fas fa-times' }}"></i>
        <span class="d-none d-lg-inline">{{ $label }}</span>
    </a>

    <!-- Modal -->
    <div class="modal fade" id="{{ $btn_id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $btn_id }}Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Exclusão de Registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">
                        <i class="{{ $icon or 'fas fa-times' }}"></i>
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>

@endif
