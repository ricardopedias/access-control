# 4. Extras

Este guia explica como configurar este pacote em uma instalação limpa do Laravel com o objetivo de desenvolvê-lo, adicionar novas funcionalidades ou corrigir bugs. Leva-se em consideração que o programador esteja utilizando terminal Unix (Linux ou Unix/Mac). Programadores que estejam usando o Windows podem executar os mesmos comandos através de emuladores de terminal. Uma ótima alternativa para Windows é o [Git Bash](https://gitforwindows.org/), que acompanha o excelente [Git for Windows](https://gitforwindows.org/).

## 4.1. Instalação do Laravel

Para desenvolver o pacote, é necessário efetuar uma instalação limpa do Laravel. Abaixo seguem os comandos para isso:

```bash
$ composer create-project --prefer-dist laravel/laravel /caminho/do/projeto
$ cd /caminho/do/projeto
$ cp .env.example .env
$ php artisan key:generate
$ chmod 777 -Rf /caminho/do/projeto/bootstrap/cache
$ chmod 777 -Rf /caminho/do/projeto/storage
```

## 4.2. A estrutura de diretórios

Na raiz do projeto Laravel, crie o diretório **packages** com o subdiretório **plexi**. Esta estrutura será usada para desenvolver pacotes:

```bash
$ mkdir -p /caminho/do/projeto/packages/plexi
```

No diretório de packages, a estrutura deve comtemplar o formato '[vendor]/[pacote]', ou seja, os pacotes deverão estruturar-se como '/plexi/access-control', '/plexi/sortable-grid', etc.

No diretório 'plexi', faça um clone do repositório:

```bash
$ cd /caminho/do/projeto/packages/plexi
$ git clone https://github.com/rpdesignerfly/access-control.git
```

## 4.3. Disponibilizando o pacote para o Laravel

No arquivo **composer.json**, abaixo da seção 'config', adicione 'minimum-stability' como 'dev' e o repositório apontando para o diretório **./packages/plexi/access-control/**.

> **Atenção:** Não esqueça da barra (/) no final:

```json
{
    "name": "laravel/laravel",
    "description": "The Laravel Framework.",

    ...


    "config": {
        ...
    },

    "minimum-stability" : "dev",
    "repositories": [
        {"type": "path", "url": "./packages/plexi/access-control/"}
    ]

}
```

Com o repositório configurado, use normalmente o composer para instalação:

```bash
$ cd /caminho/do/projeto
$ composer require plexi/access-control
```

Para mais informações, leia:

* [Package Auto Discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518)
* [Custom Packages With Auto Discovery](https://medium.com/sureshvel/laravel-5-5-custom-packages-with-autodiscover-the-providers-5772c60d847e)

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)
5. [Arquitetura](docs/05-Architecture.md)
