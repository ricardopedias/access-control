
    @include('acl::operation-message')

    <div class="row">

        <div class="col form-group">

            <label>Nome</label>
            <input name="name" type="text" value="{{ old('name', $model->name) }}"
                   class="form-control" placeholder="Digite o nome"
                   required>
            <small class="form-text text-muted">O nome legível do usuário</small>
        </div>

        <div class="col form-group">

            <label>Permissões</label>
            <select name="group_id" class="form-control" required>
                <option value="0" selected>Exclusivas deste Usuário</option>
                @foreach($groups as $item)
                    <option value="{{ $item->id }}" {{ old_option('group_id', $item->id, optional($model->groupRelation)->group_id) }}>Grupo {{ $item->name }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">Origem das permissões</small>

        </div>

        <div class="col form-group">

            <label>Email</label>
            <input name="email" type="email" value="{{ old('email', $model->email) }}"
                   class="form-control"
                   required>
            <small class="form-text text-muted">O endereço eletrônico</small>
        </div>

        <div class="col form-group">

            <label>Senha</label>
            <input name="password" type="text"
                   class="form-control"
                   {{ $require_pass }}>
            <small class="form-text text-muted">A palavra chave de acesso</small>
        </div>

    </div>

    <div class="row">

        <div class="col form-group">
            <div class="custom-control custom-checkbox custom-control-inline">
                <input type="checkbox" class="custom-control-input"
                       id="status" name="status"
                       value="active" {{ old_check('status', 'active', $model_status->status) }}>
                <label class="custom-control-label" for="status">Ativar usuário</label>
            </div>

            <div class="custom-control custom-checkbox custom-control-inline">
                <input type="checkbox" class="custom-control-input"
                       id="access_panel" name="access_panel"
                       value="yes" {{ old_check('access_panel', 'yes', $model_status->access_panel) }}>
                <label class="custom-control-label" for="access_panel">Liberar acesso ao painel</label>
            </div>
        </div>

    </div>
