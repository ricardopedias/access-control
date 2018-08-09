# 3. Como Usar

## 3.1. Configurando

Para usar o Access Control, a primeira coisa a ser feita é configurá-lo. Para isso será necessário **publicar** o arquivo de configuração a fim de permitir a sua personalização. Isso é feito através do **artisan**:

```bash
php artisan vendor:publish --tag=acl-config
```

Após a execução da operação de publicação, o arquivo `config/acl.php` poderá ser encontrado no seu projeto do Laravel.

## 3.2. O usuário ROOT

O Access Control já vem implementado com um gerenciador visual de usuários, grupos e permissões de acesso.

Por padrão, os CRUD's podem ser acessados visitando o URL */acl/users*, onde uma lista de usuários será exibida como ponto de partida. É possível criar e excluir usuários e grupos, bem como setar suas respectivas permissões.

![Permissões de acesso](imgs/crud-permissions.png?raw=true)

Inicialmente, nenhum usuário existente possuirá permissões para acessar o URL */acl/users*. Por isso, é necessário setar o ID do usuário ROOT na configuração. O usuário ROOT é um usuário especial que sempre terá acesso total ao sistema:

```php
return [

    'root_user' => 1, /* Usuário com id "1" será o ROOT */

    ...
]
```

O usuário ROOT possui acesso irrestrito, independente das permissões atribuídas a ele. Uma vez configurado o usuário ROOT, basta setar as permissões adequadas aos outros usuários do sistema e, se necessário, remover o ID de ROOT da configuração para que o usuário em questão volte ao seu 'estado normal'.

## 3.3. Funções e habilidades

Na instalação padrão do Access Control, existem 4 funções pré-configuradas com suas respectivas habilidades.

Funções (roles)      | Habilidades (permissions)
---------------------|-----------------------------
users                | create, read, update, delete
users-permissions    | create, read, update
groups               | create, read, update, delete
groups-permissions   | create, read, update

O nome de uma função é declarado nas chaves alocadas na seção **roles** do arquivo `config/acl.php`. Abaixo, observe como as funções e habilidades acima foram especificadas:

```php

'roles' => [

        'users' => [
            'label' => 'Usuários',
            'permissions' => 'create,read,update,delete',
            ],

        'users-permissions' => [
            'label' => 'Permissões de Usuários',
            'permissions' => 'create,read,update',
            ],

        'groups' => [
            'label' => 'Grupos de Acesso',
            'permissions' => 'create,read,update,delete',
            ],

        'groups-permissions' => [
            'label' => 'Permissões de Grupos',
            'permissions' => 'create,read,update',
            ],
    ]
```

As habilidades especificadas para cada função determinam sua exibição na tela de configuração de permissões de acesso, onde as habilidades disponíveis são desenhadas assim:

![CRUD com as funções](imgs/crud-roles.png?raw=true)

Usando como exemplo a função ***user-permissions***, pode-se constatar que:

```php
'users-permissions' => [
    'label' => 'Permissões de Usuários',
    'permissions' => 'create,read,update',
 ],
```

* O parâmetro **label** define o nome a ser exibido na coluna "Área de Acesso";
* O parâmetro **permissions** define quais habilidades estarão disponíveis para a configuração desta função.

Note que a função ***users*** possui as quatro habilidades (create,read,update e delete), mas a função ***users-permissions*** somente três para selecionar.

![CRUD com as habilidades](imgs/crud-roles-abilities.png?raw=true)

Cada função adicionada na seção **roles** do arquivo `config/acl.php` é usada para verificar as permissões de acesso de um usuário. Esta verificação é feita através de helpers que podem ser invocados em rotinas PHP ou em arquivos de template, diretamente nas visões do blade.

![CRUD com as funções e habilidades](imgs/crud-roles-functions-abilities.png?raw=true)

**No código PHP** é possível verificar as permissões de acesso, usando o médodo ***can*** do facade ***Auth*** do Laravel:

```php
if (\Auth::user()->can('users.update') == true) {
    echo 'Parabéns, você pode editar!!';
} else {
    echo 'Desculpe, você não pode editar!!';
}
```

**Nos templates do Blade**, de forma semelhante, as verificações condicionais podem ser efetuadas pela diretiva ***@can***:

```html
@can('users.update')
    <h1>Parabéns, você pode editar!!</h1>
@else
    <h1>Desculpe, você não pode editar!!</h1>
@endif
```

## 3.4. Diretivas especiais

Além da diretiva @can, o Access Control possui diretivas especias para controlar o acesso de várias formas dentro de templates Blade. Existem botões de acesso e delimitadores para restrição de conteúdo.

Tudo implementado usando o framework [Bootstrap 4](https://getbootstrap.com/) para oferecer maior flexibilidade e conveniência, possibilitando a personalização visual dos elementos.

### 3.4.1. Botões de Ação

São botões simples, que contém um determinando link que é disponilizado apenas se o usuário logado possuir acesso. Por exemplo:

```html
@acl_action('users.update', '/admin/users/1/edit', 'Editar Usuário')
```
No exemplo acima, ***users.update*** pede ao Access Control para verificar se a função ***users*** possui acesso à habilidade ***update***.

* Caso seja positivo, um botão será gerado com o texto *Editar Usuário* e conterá o link para '/admin/users/1/edit';
* Caso seja negativo, um botão será gerado sem o link e com ***aparência esmaecida***, indicando que o usuário não tem direito de acesso.

Existem variantes deste botão, para tamanhos diferentes, onde o sufixo ***_sm*** signifca um botão pequeno e o sufixo ***_lg***, um botão grande.

![Botões de ação](imgs/action-buttons.png?raw=true)

```html
@acl_action('users.update', '/admin/users/1/edit', 'Editar Usuário')
@acl_action_sm('users.update', '/admin/users/1/edit', 'Editar Usuário')
@acl_action_lg('users.update', '/admin/users/1/edit', 'Editar Usuário')
```

### 3.4.2. Submições de Formulário

São botões especiais, que só funcionam dentro de formulários. Por exemplo:

```html
<form action="users/update/2" meyhod="post">

    <input type="text" name="username">

    @acl_submit('users.create', 'Gravar Novo Usuário')

</form>
```
No exemplo acima, ***users.create*** pede ao Access Control para verificar se a função ***users*** possui acesso à habilidade ***create***.

* Caso seja positivo, o formulário será liberado para submissão e um botão será gerado com o texto *Gravar Novo Usuário*;
* Caso seja negativo, o ***formulário será bloqueado*** para submissão e um botão será gerado com ***aparência esmaecida***, indicando que o usuário não tem direito de acesso.

Da mesma forma que os botões de ação, existem variantes para tamanhos diferentes, onde o sufixo ***_sm*** significa um botão pequeno e o sufixo ***_lg***, um botão grande.

![Botões de submissão](imgs/submit-buttons.png?raw=true)

```html
@acl_submit('users.create', 'Gravar Dados')
@acl_submit_sm('users.create', 'Gravar Dados')
@acl_submit_lg('users.create', 'Gravar Dados')
```

### 3.4.3. Invólucro de conteúdo

Restrição de conteúdo também pode ser adicionada dentro de templates, restringindo uma parte especifica do layout, através do invólucro de conteúdo, como no exemplo abaixo:

```html
<div>

    @acl_content('users.read')

        <p>
        Conteudo html restrito!
        </p>

        <p>
        Este texto está aparecendo porque você tem permissão para vê-lo!
        </p>

    @end_acl_content

</div>
```

No exemplo acima, ***users.read*** pede ao Access Control para verificar se a função ***users*** possui acesso à habilidade ***read***.

* Caso seja positivo, o conteúdo será renderizado normalmente no template.
* Caso seja negativo, uma mensagem de **Acesso Negado** será exibida para o usuário.

![Restrição de conteúdo](imgs/content-access.png?raw=true)


## 3.5. Personalizando

No Access Control, praticamente tudo pode ser personalizado. Deste controladores até os layouts e templates do Blade podem ser facilmente manipulados, permitindo flexibilidade e liberdade na utilização das funcionalidades em qualquer projeto feito com Laravel.

### 3.5.1. Personalizando Botões

Para **Personalizar Botões**, mudando suas aparências, basta publicar uma cópia dos templates padrões usando o **artisan** com o comando abaixo:

```bash
php artisan vendor:publish --tag=acl-buttons
```

As visões personalizáveis serão geradas no diretório 'resources/views/acl/buttons'. Não é necessário que as visões estejam nesta estrutura específica de diretórios. Pode-se mudar a localização para se adequar ao projeto.

Para renderizar um **botão de ação** com uma visão personalizada, basta especificar a localização dela como quarto parâmetro da diretiva **@acl_action**:

```html
@acl_action('users.update', '/admin/users/1/edit', 'Editar Usuário', 'acl.buttons.botao-de-edicao')
```

Para renderizar um **botão de submissão de formulário** com uma visão personalizada, basta especificar a localização dela no terceiro parâmetro da diretiva **@acl_submit**:

```html
@acl_submit('users.create', 'Gravar Novo Usuário', 'acl.buttons.botao-de-criacao')
```

### 3.5.2. Personalizando CRUD's

Para **Personalizar Formulários e Grids**, mudando a aparência dos CRUD's de gerenciamento de usuários, grupos e permissões, basta publicar uma cópia dos templates padrões usando o **artisan** com o comando abaixo:

```bash
php artisan vendor:publish --tag=acl-cruds
```

As visões personalizáveis serão geradas no diretório 'resources/views/acl/cruds'. Assim como os botões, não é necessário que as visões estejam nesta estrutura específica de diretórios (*resources/views/acl/cruds*). Pode-se mudar a localização para se adequar ao projeto.

Para declarar ao Access Control que ele deve usar estas novas views, é preciso configurá-las na seção **views** do arquivo `config/acl.php`.


```php
return [

    ...

    'views' => [

        'users' => [
            'index'  => 'acl.cruds.index',   /* view local personalizada */
            'create' => 'acl::users.create', /* view do pacote acl (::) */
            'edit'   => 'acl::users.edit',   /* view do pacote acl (::) */
        ],

        ...

    ],

    ...

]

```

> **Nota**: As views publicadas, por se tratarem de cópias das views internas do Acl, possuem chamadas para o pacote 'acl::'. Para usar as mesmas views e componentes de forma local, mude as invocações 'acl::' para 'acl.cruds', ou para outra localização de sua preferência.

### 3.5.3. Personalizando funções e habilidades

Novas funções e habilidades podem ser adicionadas na seção **roles** do arquivo `config/acl.php`. Cada função deve possuir a sua chave em ***slug case*** (ex: minha-funcao). Como valores desta chave, devem existir dois parâmetros (label e permissions):

```php
return [

    ...

    'roles' => [

        'articles' => [                                   // <-- A slug da função
            'label'       => 'Artigos',                   // <-- O nome para exibição
            'permissions' => 'create,read,update,delete', // <-- As habilidades configuráveis
        ],

        ...

    ]

    ...

]

```

### 3.5.4. Personalizando rotas

As rotas padrões possuem as urls com o prefixo 'acl' seguido da rota básica (*acl/users* ou *acl/users-permissions*). Isso pode ser facilmente mudado, setando urls personalizadas na seção **routes** do arquivo `config/acl.php`:

```php
return [

    ...

    'routes'     => [
        'users'              => 'meu-painel/usuarios', /* rota personalizada */
        'users-permissions'  => 'acl/users-permissions',
        'groups'             => 'acl/groups',
        'groups-permissions' => 'acl/groups-permissions',
    ],

    ...
]

```

### 3.5.5. Personalizando controladores

Os controladores também podem ser personalizados, setando-os adequadamente na seção **controllers** do arquivo `config/acl.php`:


```php
return [

    ...

    'controllers'     => [
        'users'              => 'App\Http\Controllers\MeuUsersController', /* controlador personalizado */
        'users-permissions'  => 'Acl\Http\Controllers\UsersPermissionsController',
        'groups'             => 'Acl\Http\Controllers\GroupsController',
        'groups-permissions' => 'Acl\Http\Controllers\GroupsPermissionsController',
    ],

    ...
]
```

Isso permite mudar ou expandir as funcionalidades de um determinado controlador, possibilitando a sobrecarga ou abstação do controlador original do Access Control:

```php
class MeuUsersController extends \Acl\Http\Controllers\UsersController
{
    public function store(Request $form)
    {
        // Faz a validação de um campo adicional personalizado
        // no banco de dados e proveniente de uma view personalizada
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
