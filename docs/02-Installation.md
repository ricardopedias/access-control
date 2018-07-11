# 2. Instalação

## Requisitos do servidor

O pacote Acl possui os seguintes requisitos básicos:

* PHP >= 7.0.0
* Laravel >= 5.5
* Banco de Dados MySQL
* Extensão PDO do PHP

## Baixando o pacote e as dependências

Para baixar o pacote, será necessário usar o [Composer](http://getcomposer.org/).
Com o composer devidamente instalado no sistema operacional, execute o seguinte comando: 

```bash
$ cd /diretorio/meu/projeto/laravel/
$ composer require plexi/access-control
```

O comando acima vai adicionar automaticamente a chamada para a última versão do Acl no 
arquivo composer.json do Laravel e em seguia efetuar o processo de instalação.

Para instalar uma versão específica, basta substituir pelo comando:

```bash
$ composer require plexi/access-control:1.1.5
```

## Atualizando o banco de dados 

O Acl precisa de algumas tabelas adicionais no banco de dados para o gerenciamento das permissões.
Para adicioná-las ao seu banco de dados será necessário executar as [Migrações](https://laravel.com/docs/5.6/migrations) 
contidas no pacote plexi/access-control.

Existem duas maneiras de fazer isso, publicando as migrações ou rodando-as diretamente do pacote:

### Publicando as migrações

Para publicar as migrações, basta usar o comando abaixo:

```bash
$ php artisan vendor:publish --tag=acl-migrations
```
As seguintes migrações serão adicionadas ao diretório `database/migrations`:

```bash
2018_02_14_000000_create_acl_groups_table.php
2018_02_14_000001_update_users_acl_group_field.php
2018_02_14_000002_create_acl_roles_table.php
2018_02_14_000003_create_acl_users_permissions_table.php
2018_02_14_000004_create_acl_groups_permissions_table.php
```

Em seguida, basta rodar as migrações normalmente para gerar as tabelas:

```bash
$ php artisan migrate
```

### Sem publicar as migrações

```bash
$ php artisan migrate --path=vendor/plexi/access-control/src/database/migrations
```

Esta operação criará quatro tabelas:

* acl_groups
* acl_groups_permissions
* acl_roles
* acl_users_permissions

A operação também atualizará a tabela users, nativa do Laravel, adicionando a coluna:

* acl_group_id

Esta coluna servirá para especificar a identificação do grupo de acesso ao qual o usuário pertencerá.

## Testando a instalação

O Acl possui CRUDs já implementados para o gerenciamento de usuários e grupos de acesso.
Para acessá-los, basta seguir a url:

```text
http://www.meuprojeto.com.br/acl/users
```

Nota: troque o domínio do exemplo ('meuprojeto.com.br') para o domínio onde o seu projeto Laravel está instalado.

## Revertendo/Limpando o banco de dados

Para remover as alterações efetuadas no bancod e dados:

```bash
$ php artisan migrate:reset --path=vendor/plexi/access-control/src/database/migrations
```

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)
