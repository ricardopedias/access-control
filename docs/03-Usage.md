# 3. Como Usar

## Configurando

A primeira coisa a se fazer é efetuar a configuração básica do "Access Control". 
Isso é realizado no arquivo de configuração que deve ser publicado através do seguinte comando:

```bash
php artisan vendor:publish --tag=acl-config
```

Após executar este comando, o arquivo de configuração poderá ser encontrado em `config/acl.php.`

## O usuário ROOT

Para acessar os CRUD's, é preciso configurar as permissões dos usuários ou grupos de acesso para que estes possam acessar as páginas.
Na instalação inicial, nenhum usuário possui permissões. Por isso, é necessário setar o ID do usuário ROOT na configuração:

```php

return [

    'root_user' => 1, // <--- Usuário com id "1" será o ROOT

    ...
```

O usuário ROOT possui acesso total, independente das permissões atribuídas a ele. 
Uma vez configurado o usuário ROOT, basta setar as permissẽos adequadas aos outros usuários do sistema e, 
se necessário, remover o ID de ROOT para o usuário voltar ao seu 'estado normal'.

## Usando as funções e habilidades

Por padrão, existem 4 funções com suas respectivas habilidades, que podem ser usados em qualquer projeto Laravel. 
Essas funções são gerenciáveis visualmente através dos CRUD's do Acl:

Função             | Habilidades
-------------------|-----------------------------
users              | create, read, update, delete
users-permissions  | create, read, update
groups             | create, read, update, delete
groups-permissions | create, read, update

Cada função pode ser chamada dentro da implementação de um projeto Laravel para verificar se o usuário atual tem ou não direito de acesso a determinada área.

## Diretivas para layout no Blade

O "Access Control" possui diretivas especias para controlar o acesso diretamente em templates do blade.
São botões de acesso e delimitadores para restrição de conteúdo. Tudo é implementado usando o framework [Bootstrap 4](https://getbootstrap.com/).

### Personalização

Para personalizar a aparência dos botões, basta publicar uma cópia dos templates padrões. 
Usando o comando abaixo, as views personalizáveis serão geradas no diretório 'resources/views/acl/buttons':

```bash
php artisan vendor:publish --tag=acl-buttons
```

Não é necessário que as views estejam nesta estrutura de diretórios, pois as views personalizadas 
são setadas no momento da exibição do botão e podem possuir qualquer caminho de localização.

### Botões de Ação

São botões simples, que contém um determinando link. Por exemplo:

```html
@acl_action('users.edit', '/admin/users/1/edit', 'Editar Usuário')
```
No exemplo acima, ***users.edit*** diz ao Acl para verificar se a função ***users*** possui acesso à habilidade ***edit***.
Caso seja positivo, um botão será gerado com o texto 'Editar Usuário' e conterá o link para '/admin/users/1/edit'. 
Caso seja negativo, um botão será gerado sem o link e com aparência esmaecida, indicando que o usuário não tem direito de acesso.

Existem variantes deste botão, para tamanhos diferentes, onde o sufixo ***_sm*** signifca um botão pequeno e o sufixo ***_lg***, um botão grande:

```html
@acl_action('users.edit', '/admin/users/1/edit', 'Editar Usuário')
@acl_action_sm('users.edit', '/admin/users/1/edit', 'Editar Usuário')
@acl_action_lg('users.edit', '/admin/users/1/edit', 'Editar Usuário')
```

Os botões usam um template padrão, baseado no [Bootstrap 4](https://getbootstrap.com/). 
Para especificar um template personalizado, basta fornecer um quarto parâmetro com a view desejada e o 
botão será renderizado com ela.

```html
@acl_action('users.edit', '/admin/users/1/edit', 'Editar Usuário', 'meus-botoes.botao-de-edicao')
```

### Botões de Submissão de Formulário

São botões especiais, que só funcionam dentro de formulários. Por exemplo:

```html
<form action="users/update/2" meyhod="post">

    <input type="text" name="username">

    @acl_submit('users.create', 'Gravar Novo Usuário') 
    
</form>
```
No exemplo acima, ***users.create*** verifica se a função ***users*** possui acesso à habilidade ***create***.
Caso seja positivo, o formulário será liberado para submissão e um botão será gerado com o texto 'Gravar Novo Usuário'. 
Caso seja negativo, o formulário será bloqueado para submissão e um botão será gerado com aparência esmaecida, indicando que o usuário não tem direito de acesso.

Da mesma forma que os botões de ação, existem variantes para tamanhos diferentes, onde o sufixo ***_sm*** significa um botão pequeno e o sufixo ***_lg***, um botão grande:

```html
@acl_submit('users.create', 'Gravar Novo Usuário') 
@acl_submit_sm('users.create', 'Gravar Novo Usuário') 
@acl_submit_lg('users.create', 'Gravar Novo Usuário') 
```

Para especificar um template personalizado, basta fornecer um terceiro parâmetro com a view desejada e o 
botão será renderizado com ela.

```html
@acl_submit('users.create', 'Gravar Novo Usuário', 'meus-botoes.botao-de-criacao') 
```

### Restrição de conteúdo

Também é possível restringir uma parte especifica de um layout, usando o invólucro de conteúdo, como no exemplo abaixo:

```html
<div>

    @acl_content('users.read')

        <p>
        Conteudo html restrito!
        </p>

        <p>
        Aparece apenas para usuários que tem permissão para leitura!
        </p>

    @end_acl_content
    
</div>
```

No exemplo acima, ***users.read*** verifica se a função ***users*** possui acesso à habilidade ***show***.
Caso seja positivo, o conteúdo será renderizado normalmente no template. 
Caso seja negativo, uma mensagem de 'Acesso Negado' será exibida para o usuário.

## Restrições condicionais

As verificações condicionais são efetuadas através do método 'can', presente no objeto de autenticação padrão do Laravel.
O método está disponível tanto no Blade como no ambiente PHP:

### No blade

A diretiva '@can' pode ser usada para efetuar verificações de acesso, bastando passar a função e a habilidade desejada como parâmetro:

```html
@can('users.edit')

    <h1>Parabéns, você pode editar!!</h1>

@else

    <h1>Desculpe, você não pode editar!!</h1>

@endif
```

### No PHP

Dentro das rotina de programação também é possível verificar as permissões de acesso, usando o médodo 'can' do facade 'Auth' do Laravel: 

```php
if ( \Auth::user()->can('users.update') == true) {
    echo 'Parabéns, você pode editar!!';
}
else {
    echo 'Desculpe, você não pode editar!!';
}
```

## Adicionando funções e habilidades

Novas funções e habilidades devem ser adicionadas na seção ***roles*** do arquivo de configuração.
Cada habilidade deve possuir a sua slug, seguida de dois parâmetros, sendo:

```php
return [

    ...

    'roles' => [

        'users' => [                                    // <-- A slug da função
            'label'       => 'Usuários',                // <-- O nome para exibição
            'permissions' => 'create,read,update,delete', // <-- As habilidades configuráveis
        ],

    ...

```

## Personalizando os CRUDs

Para adicionar flexibilidade, e possibilitar a adaptação a qualquer projeto, o Acl permite configurar os CRUDs de configuração das permissões. Entre as personalizações, pode-se alterar as rotas, os controladores e as views usadas pelo mecanismo interno. 

### Rotas Personalizados

As rotas padrões possuem as urls com o prefixo 'acl' seguido da rota básica ('acl/users' ou 'acl/users-permissions').
Isso pode ser facilmente mudado, setando urls personalizadas na seção 'routes' do arquivo de configuração:

```php
return [

    ...

    'routes'     => [
        'users'              => 'meu-painel/usuarios', // <-- rota personalizada
        'users-permissions'  => 'acl/users-permissions',
        'groups'             => 'acl/groups',
        'groups-permissions' => 'acl/groups-permissions', 
    ],

    ...

```

### Visões Personalizadas

Para personalizar a aparência dos CRUD's, basta publicar uma cópia dos templates padrões. 
Usando o comando abaixo, as views personalizáveis serão geradas no diretório 'resources/views/acl/cruds':

```bash
php artisan vendor:publish --tag=acl-cruds
```

> **Nota**:
> As views publicadas, por se tratarem de cópias das views internas do Acl, possuem chamadas para o pacote 'acl::'. Para usar as mesmas views e componentes de forma local, mude as invocações 'acl::' para 'acl.cruds.'.

Não é necessário que as views estejam nesta estrutura de diretórios, pois as views personalizadas 
são configuradas manualmente na seção ***views*** do arquivo de configuração:

```php
return [

    ...

    'views' => [

        'users' => [
            'index'  => 'acl.cruds.index',   // <-- view personalizada
            'create' => 'acl::users.create', // <-- view do pacote acl (::)
            'edit'   => 'acl::users.edit',   // <-- view do pacote acl (::)
        ],

        ...

    ],

    ...

```

### Controlladores Personalizados

Os controladores também podem ser personalizados, setando-os na seção 'controllers':

```php
return [

    ...

    'controllers'     => [
        'users'              => 'App\Http\Controllers\MeuUsersController', // <-- controlador personalizado
        'users-permissions'  => 'Acl\Http\Controllers\UsersPermissionsController',
        'groups'             => 'Acl\Http\Controllers\GroupsController',
        'groups-permissions' => 'Acl\Http\Controllers\GroupsPermissionsController',
    ],

    ...

```

O aproveitamento das funcionalidades padrões é feita facilmente, extendendo o controlador original do Acl:

```php
class MeuUsersController extends \Acl\Http\Controllers\UsersController
{
    public function store(Request $form)
    {
        // Faz a validação de uma informação adicional
        // proveniente de uma view personalizada
        $form->validate([
            'blog_id' => 'required|int',
        ]);

        // Invoca o método store padrão do Acl
        return parent::store($form);
    }
}
```

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)