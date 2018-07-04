
    @if ($errors->any())

        <div class="alert alert-warning">

            @foreach ($errors->all() as $error)
                <i class="fa fa-angle-right"></i> {{ $error }} <br>
            @endforeach

        </div>

    @endif

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
