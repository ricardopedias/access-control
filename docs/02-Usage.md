# 2. Como Usar

## Configurando

A primeira coisa a se fazer é efetuar a configuração básica do Laracl. 
Isso é realizado no arquivo de configuração que deve ser publicado através do seguinte comando:

```
php artisan vendor:publish --tag=config-laracl
```

Após executar este comando, o arquivo de configuração poderá ser encontrado em config/laracl.php.

## O usuário ROOT

Para acessar os CRUD's, é preciso configurar as permissões de acesso aos usuários ou grupos de acesso paar que estes possam acessar as páginas.
Na instalação inicial, nenhum usuário possui permissões. Por isso, é necessário setar o ID do usuário ROOT na configuração:

```
<?php
return [

    'root_user' => 1, // <--- Usuário 1 será o ROOT

    ...
```

O usuário ROOT possui acesso total, independente das permissões atribuídas a ele. 
Uma vez configurado o usuário ROOT, basta setar as permissẽos adequadas aos usuários e, 
se necessário, remover o ID de ROOT para o usuário voltar ao seu 'estado normal'.

## Usando as funções e habilidades

Por padrão, existem 4 funções com suas respectivas habilidades. Essas funções são 
gerenciaveis visualmente através dos CRUD's do Laracl:

* users - create, edit, show, delete
* users-permissions - create, edit, show
* groups - create, edit ,show, delete
* groups-permissions - create, edit, show

Onde 'users' é a função e 'create, edit, show, delete' são as habilidades.

Cada função pode ser chamada dentro da implementação de um projeto Laravel para verificar 
se o usuário atual tem ou não direito de acesso a determinada área.

### Permissões em Views

...


### Permissões em Controllers

...


## Adicionando funções e habilidades

Novas funções e habilidades devem ser adicionadas na seção 'roles' do arquivo de configuração.
Cada habilidade deve possuie a sua slug, seguida de dois parâmetros, sendo:

```
<?php
return [

    ...

    'roles' => [

        'users' => [                                    <-- A slug da função
            'label'       => 'Usuários',                <-- O nome para exibição da função nos CRUD's
            'permissions' => 'create,edit,show,delete', <-- As habilidades configuráveis nos CRUD's
        ],

    ...

```


## Personalizando os CRUDs

É possível personalizar as funcionalidades dos CRUDs do Laracl. Entre as personalizações, pode-se alterar as rotas, os controladores e as views usadas pelo mecanismo interno. Isso oferece liberdade e flexibilidade para adequar o Laracl a qualquer 
projeto Laravel existente.

### Rotas Personalizados

...


### Controlladores Personalizados

...

### Visões Personalizadas

...


## Sumário

1. [Sobre](00-Home.md)
2. [Instalação](01-Installation.md)
3. [Como Usar](02-Usage.md)
4. [Exemplos](03-Examples.md)
5. [Extras](04-Extras.md)