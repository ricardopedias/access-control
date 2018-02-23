# 2. Instalação

## Sumário

1. [Sobre](00-Home.md)
2. [Instalação](01-Installation.md)
3. [Como Usar](02-Usage.md)
4. [Exemplos](03-Examples.md)
5. [Extras](04-Extras.md)

## Requisitos do servidor

O pacote Laracl possui os seguintes requisitos:

* PHP >= 7.0.0
* Laravel >= 5.5

## Baixando o pacote e as dependências

Para baixar o pacote, será necessário usar o [Composer](http://getcomposer.org/).
Com o composer devidamente instalado no sistema operacional, execute o seguinte comando: 

```
$ cd /diretorio/meu/projeto/laravel/
$ composer require plexi/laracl
```

O comando acima vai adicionar automaticamente a chamada para a última versão do Laracl no 
arquivo composer.json do Laravel e em seguia efetuar o processo de instalação.

Para instalar uma versão específica, basta substituir pelo comando:

```
$ composer require plexi/laracl:1.1.0
```

## Atualizando o banco de dados

O Laracl precisa de algumas tabelas adicionais no banco de dados para o gerenciamento das permissões.
Para adicioná-las ao seu banco de dados será necessário executar as [Migrações](https://laravel.com/docs/5.6/migrations) 
contidas no pacote plexi/laracl:

```
$ php artisan migrate --path=vendor/plexi/laracl/src/database/migrations
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

O Laracl possui CRUDs já implementados para o gerenciamento de usuários e grupos de acesso.
Para acessá-los, basta seguir a url:

```
http://www.meuprojeto.com.br/laracl/users
```

*** Nota: troque o domínio 'meuprojeto.com.br' para o domíbio onde o seu projeto Laravel está instalado. ***









