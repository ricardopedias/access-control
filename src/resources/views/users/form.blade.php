
    <div class="row">

        <div class="col form-group">

            <label>Nome</label>
            <input name="name" type="text" value="{{ old('name', $model->name) }}"
                   class="form-control" placeholder="Digite o nome"
                   required>
            <small class="form-text text-muted">O nome legível do usuário</small>
        </div>

        <div class="col form-group">

            <label>Grupo</label>
            <select name="acl_group_id"
                   class="form-control" required>

                @foreach($groups as $item)
                    <option value="{{ $item->id }}" {{ old_option('acl_group_id', $item->id, $model->acl_group_id) }}>{{ $item->name }}</option>
                @endforeach

            </select>
            <small class="form-text text-muted">O grupo deste usuário</small>
        </div>

        <div class="col form-group">

            <label>Email</label>
            <input name="email" type="email" value="{{ old('email', $model->email) }}"
                   class="form-control" 
                   required>
            <small class="form-text text-muted">O endereço eletrônico</small>
        </div>

        <div class="col form-group">

            <label>Usuário</label>
            <input name="username" type="text" value="{{ old('username', $model->username) }}"
                   class="form-control" placeholder="Digite um número"
                   required>
            <small class="form-text text-muted">O nome de usuário</small>
        </div>

        <div class="col form-group">

            <label>Senha</label>
            <input name="password" type="text" 
                   class="form-control" >
            <small class="form-text text-muted">A palavra chave de acesso</small>
        </div>

    </div>

