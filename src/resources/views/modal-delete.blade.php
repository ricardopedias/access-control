
<!-- Modal -->
<div class="modal" id="acl-delete-confirm" tabindex="-1" role="dialog" aria-labelledby="acl-delete-confirm-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acl-delete-confirm-title">
                    Excluir Registro
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <p id="acl-delete-confirm-message-default" class="m-4">
                    Esta operação irá excluir o registro. O que deseja fazer?
                </p>

                <p id="acl-delete-confirm-message-error" class="m-4 text-danger">
                    Um erro aconteceu ao tentar excluir este registro.
                    Por favor, tente novamente mais tarde!
                </p>

                <div id="acl-delete-confirm-message-confirm" class="text-danger text-center" style="display: none">
                    <p>
                        Esta operação não tem volta!
                        Tem certeza disso?
                    </p>

                    <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal">
                        Não Excluir
                    </button>

                    <button type="button" class="btn btn-lg btn-danger"
                        id="acl-delete-confirm-btn-confirm">
                        <i class="fas fa-thumbs-up"></i>
                        Sim, Excluir!
                    </button>
                </div>

                <div id="acl-delete-confirm-message-progress"
                    class="progress m-4" style="display: none; height: 5px;">
                    <div class="progress-bar bg-danger" role="progressbar"
                         aria-valuemin="0" aria-valuemax="100"
                         aria-valuenow="0"
                         style="width: 0%; height: 5px;"></div>
                </div>
            </div>

            <div id="acl-delete-confirm-buttons" class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Não Excluir
                </button>

                @if(config('acl.soft_delete') != false and \Acl\Core::isTrash() == false)
                    <button id="acl-delete-confirm-btn-trash"
                            type="button" class="btn btn-info js-acl-delete-confirm-action"
                            data-content="Este registro será enviado para a lixeira, podendo ser recuperado depois.">
                        <i class="{{ $icon or 'fas fa-trash' }}"></i>
                        Mover para o Lixo
                    </button>
                @endif

                <button id="acl-delete-confirm-btn-delete"
                        type="button" class="btn btn-danger js-acl-delete-confirm-action"
                        data-content="ATENÇÃO: Este registro e todos o dados ligados a ele serão removidos permanentemente!">
                    <i class="{{ $icon or 'fas fa-times' }}"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<script>

    //
    // Esta classe gera o objeto para a criação
    // do modal de confirmação de exclusão.
    //
    var AclConfirmDelete = function(debug){
        this.debugMode(debug);
    };
    AclConfirmDelete.prototype = {

        debug: false,

        token: null,

        remove_row: false,

        btn_elem: null,

        /**
         * Ativa/desativa o modo de debug.
         * Quando ativo, as mensagens de log e erros de servidor serão exibidos,
         * caso contráio, tudo será ocultado do usuário final.
         * @param {boolean} mode
         */
        debugMode: function(mode)
        {
            this.debug = (undefined !== mode && mode === true) ? true : false;
            this.trace('Debug: ' + this.debug);
        },

        /**
         * Fornece o csrf_token do laravel para o objeto utilizar na requisição.
         * @see https://laravel.com/docs/5.6/csrf
         * @param {string} csrf_token
         */
        setToken: function(csrf_token)
        {
            this.token = csrf_token;
            this.trace('Token: ' + this.token);
        },

        /**
         * Ativa/desativa a remoção da linha no grid de dados
         * após a resposta do servidor.
         * @param {boolean} remove
         */
        removeGridRow: function(remove)
        {
            this.remove_row = remove;
            this.trace('Remove Grid Row: ' + remove);
        },

        /**
         * Libera uma mensagem de log do navegador.
         * @param {string} message
         */
        trace: function(message)
        {
            if(this.debug ===true) {
                console.log(message);
            }
        },

        /**
         * Volta o modal para seu estado original.
         */
        reset: function()
        {
            // zera os valores de progresso
            $.each(['w-25', 'w-50', 'w-75', 'w-100'], function(k, class_name){
                $('#acl-delete-confirm-message-progress .progress-bar').removeClass(class_name);
            });

            // exibe os botões
            $('#acl-delete-confirm-buttons').show();

            // exibe a mensagem inicial
            this.message('default');
        },

        /**
         * Alterna entre as mensagens do modal, exibindo a especificada na variável 'block'.
         * Os tipos de mensagens para a variável block são 'default','confirm','progress' e 'error'.
         * Se o bloco for do tipo 'error', a variável 'message' poderá ser especificada com um
         * texto personalizado contendo o erro a ser reportado no modal.
         * @param {string} block
         * @param {string} message
         */
        message: function(block, message)
        {
            $.each(['default','error','confirm','progress'], function(k, name){
                $('#acl-delete-confirm-message-' + name).hide();
            });
            $('#acl-delete-confirm-message-' + block).show();

            if( block === 'error') {
                var error_message = (undefined !== message)
                    ? 'Um erro aconteceu ao tentar excluir este registro.<br>' + message
                    : 'Um erro aconteceu ao tentar excluir este registro. Por favor, tente novamente mais tarde!';
                $('#acl-delete-confirm-message-error').html(error_message);
            }
        },

        /**
         * Executa a requisição da url existente no botão delete.
         * O argumento url é especificado no botão de ação 'delete'.
         * O argumento mode informa o modo de exclusão solicitada, podendo ser 'soft' ou 'hard''
         * @param {string} url
         * @param {string} mode
         */
        run: function(url, mode)
        {
            var self = this;

            self.trace('Request: ' + url);

            // Oculta os botões
            $('#acl-delete-confirm-buttons').hide();

            var progress_bar = $('#acl-delete-confirm-message-progress .progress-bar');

            // zera os valores
            $.each(["w-25", "w-50", "w-75"], function(k, class_name){
                $('#acl-delete-confirm-message-progress .progress-bar').removeClass(class_name);
            });

            // seta o valor inicial
            var progress_values = ["w-25", "w-50", "w-75"];
            var init = progress_values[Math.floor(Math.random()*progress_values.length)];
            $(progress_bar).addClass(init);

            $.ajax({
                  url: url,
                  data: {
                      _method: 'DELETE', // https://laravel.com/docs/5.5/controllers#resource-controllers
                      _token: self.token, // https://laravel.com/docs/5.6/csrf
                      mode: mode
                  },
                  method: 'post',
                  success: function(data, text_status, jq_xhr) {

                      self.trace('Response: success');

                      $('#acl-delete-confirm-message-progress .progress-bar').addClass('w-100');

                      self.afterDelete();
                  },
                  error: function(jq_xhr, text_status, error_thrown) {

                      if (self.debug === true) {
                          self.trace('Response: debugged error');
                          self.message('error', jq_xhr.responseJSON.message);
                      } else {
                          self.trace('Response: error');
                          self.message('error');
                      }
                  }
            });
        },

        /**
         * Este método é chamado quando um registro for excluido com sucesso.
         * Se o parâmetro remove_row for passado como true na diretiva do blade
         * a linha do grid será removida. caso contrário, a página será recarregada
         */
        afterDelete: function()
        {
            var self = this;

            if(this.remove_row === true) {

                this.trace('Remove Row: ' + this.remove_row);
                var row = $(self.btn_elem).parents('tr').slideUp(200, function(){
                    this.remove();
                });

                setTimeout(function(){
                    $('#acl-delete-confirm').modal('hide');
                }, 1000);

            } else {
                this.trace('Reload Page: true');
                setTimeout(function(){
                    window.location.reload();
                }, 500);
            }
        },

        /**
         * Aplica o evento de clique no botão de ação.
         * O argumento 'btn_elem' pode ser um identificador css, por ex: #meu-botao
         * ou pode ser o objeto de um elemento html.
         * @param {string|object} btn_elem
         */
        attach: function(btn_elem)
        {
            var self = this;

            this.btn_elem = $(btn_elem);
            self.trace('Attach: ' + $(btn_elem).attr('id'));

            $(btn_elem).click(function(event){

                self.trace('Clicked: ' + $(btn_elem).attr('id'));

                self.reset();

                event.preventDefault();
                var url = $(this).data('url');

                $('#acl-delete-confirm-btn-trash').unbind('click').bind('click', function(){
                    self.trace('To trash: ' + url);
                    self.message('progress');
                    self.run(url, 'soft');
                });

                $('#acl-delete-confirm-btn-confirm').unbind('click').bind('click', function(){
                    self.trace('Do remove: ' + url);
                    self.message('progress');
                    self.run(url, 'hard');
                });

                $('#acl-delete-confirm-btn-delete').unbind('click').bind('click', function(){
                    self.trace('Confirm: ' + url);
                    $('#acl-delete-confirm-buttons').hide();
                    self.message('confirm');
                });

                $('#acl-delete-confirm').addClass('fade').modal('show');
            });

            $('.js-acl-delete-confirm-action').popover({
                container : "body",
                placement: "top"
            }).hover(function(){ $(this).popover('show');}, function(){ $(this).popover('hide'); });
        }
    };

</script>
