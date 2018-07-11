# 2. Instalação

## Requisitos do servidor

O pacote Access Control possui os seguintes requisitos básicos:

* PHP >= 7.0.0
* Laravel >= 5.6
* Banco de Dados MySQL
* Extensão PDO do PHP

## Baixando o pacote e as dependências

Para baixar o pacote, será necessário usar o [Composer](http://getcomposer.org/).
Com o composer devidamente instalado no sistema operacional, execute o seguinte comando para isntalar a última versão do Access Control: 

```bash
$ cd /diretorio/meu/projeto/laravel/
$ composer require plexi/access-control
```

Para instalar uma versão específica, basta substituir pelo comando:

```bash
$ composer require plexi/access-control:2.0.0
```

Os comandos acima vão adicionar automaticamente a chamada para o pacote no arquivo composer.json do Laravel e em seguida efetuar o processo de instalação.


## Atualizando o banco de dados 

O Access Control precisa de algumas tabelas adicionais no banco de dados para o gerenciamento das permissões.
Para adicioná-las ao seu banco de dados será necessário executar as [Migrações](https://laravel.com/docs/5.6/migrations) contidas no pacote plexi/access-control.

Existem duas maneiras de fazer isso, publicando as migrações ou rodando-as diretamente do pacote:

### Publicando as migrações

Para publicar as migrações, basta usar o comando abaixo:

```bash
$ php artisan vendor:publish --tag=acl-migrations
```
O seguinte arquivo de migração será adicionado ao diretório `database/migrations`:

```bash
2018_03_20_000001_create_acl_tables.php
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
* acl_users_groups
* acl_users_permissions
* acl_users_status

O "Access Control" possui suporte a soft deletes, por isso, precisa que o campo "deleted_at" exista 
na tabela "users" (nativa do Laravel). A operação de migração também atualizará a tabela users, 
adicionando a coluna "deleted_at" se ela não existir:


## Testando a instalação

O Acl possui CRUDs já implementados para o gerenciamento de usuários e grupos de acesso.
Para acessá-los, basta seguir a url:

```text
http://www.meuprojeto.com.br/acl/users
```

Nota: troque o domínio do exemplo ('meuprojeto.com.br') para o domínio onde o seu projeto Laravel está instalado.

## Revertendo/Limpando o banco de dados

Para remover as alterações efetuadas no banco de dados basta:

Se você publicou o arquivo "2018_03_20_000001_create_acl_tables.php":

```bash
$ php artisan migrate:reset
```

Se você não publicou:

```bash
$ php artisan migrate:reset --path=vendor/plexi/access-control/src/database/migrations
```

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)
