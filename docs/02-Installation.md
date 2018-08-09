# 2. Instalação

## 2.1. Requisitos do usuário

Este guia leva em consideração que os usuários estejam utilizando terminal Unix (Linux ou Unix/Mac). Usuários que estejam usando Windows podem executar os mesmos comandos através de emuladores de terminal. Uma ótima alternativa para Windows é o [Git Bash](https://gitforwindows.org/), que acompanha o excelente [Git for Windows](https://gitforwindows.org/).

## 2.2. Requisitos do servidor

Para o correto funcionamento, o Access Control precisa que os seguintes requisitos básicos sejam atendidos, estando disponíveis no servidor:

* PHP >= 7.1.0
* Laravel >= 5.6
* Extensão PDO do PHP
* Banco de Dados MySQL

## 2.3. Instalando o pacote

O Access Control se encontra no [Packagist](https://packagist.org/), podendo ser alocado facilmente em qualquer projeto Laravel através do [Composer](http://getcomposer.org/).

Com o composer devidamente instalado no sistema operacional do desenvolvedor, execute o seguinte comando para instalar a última versão do Access Control:

```bash
$ cd /diretorio/meu/projeto/laravel/
$ composer require plexi/access-control
```

Se preferir instalar uma versão específica, basta substituir pelo comando:

```bash
$ composer require plexi/access-control:2.0.0
```

Os comandos acima vão adicionar automaticamente a chamada para o pacote no arquivo **composer.json** que acompanha o Laravel, excutando em seguida o processo de instalação.

## 2.4. Preparando o banco de dados

### 2.4.1. Executando as migrações

O Access Control utiliza banco de dados para armazenar as informações sobre os usuários, grupos e suas permissões. Para configurar as tabelas necessárias, adicionando-as ao seu banco de dados, será preciso executar as [Migrações](https://laravel.com/docs/5.6/migrations) que acompanham o pacote *plexi/access-control*.

A partir da versão 5.6, o Laravel [executa as migrações dos pacotes](https://laravel.com/docs/5.6/packages#resources) instalados de forma transparente, bastando usar o **artisan**:

```bash
$ php artisan migrate
```

Esta operação criará seis tabelas:

* acl_groups
* acl_groups_permissions
* acl_roles
* acl_users_groups
* acl_users_permissions
* acl_users_status

O *Access Control* possui suporte a [Soft Deletes](https://laravel.com/docs/5.6/eloquent#soft-deleting), por isso, é necessário que o campo ***deleted_at*** exista na tabela ***users*** (nativa do Laravel).

O processo de migração atualizará a tabela *users*, adicionando a coluna *deleted_at* se ela não existir.

### 2.4.2. Formas alternativas

Embora não sejam tão práticas, existem outras duas maneiras de trabalhar com as migrações:

* Publicando-as;
* Rodando-as diretamente do pacote

Para **Publicar as migrações**, basta usar o comando abaixo:

```bash
$ php artisan vendor:publish --tag=acl-migrations
```
O seguinte arquivo de migração será adicionado ao diretório `database/migrations`:

```bash
2018_03_20_000001_create_acl_tables.php
```

Em seguida, basta rodar as migrações do projeto normalmente para gerar as tabelas:

```bash
$ php artisan migrate
```

Para **Rodar as migrações diretamente do pacote** basta usar o comando abaixo:

```bash
$ php artisan migrate --path=vendor/plexi/access-control/src/database/migrations
```

## 2.5. Testando a instalação

O Access Control possui CRUDs já implementados para o gerenciamento de usuários e grupos de acesso. Para vê-los, basta acesssar o seguinte endereço:

```text
http://www.meuprojeto.com.br/acl/users
```
> **Nota**: troque o domínio do exemplo ('meuprojeto.com.br') pelo domínio onde o seu projeto Laravel está instalado.

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)
