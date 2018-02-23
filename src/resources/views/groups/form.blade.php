
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

            <label>Description</label>
            <input name="description" type="text" value="{{ old('description', $model->description) }}"
                   class="form-control" 
                   required>
            <small class="form-text text-muted">Uma descrição curta para o grupo</small>
        </div>

        <input type="hidden" name="system" value="no">
        
    </div>

