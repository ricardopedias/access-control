# 4. Extras

## Para desenvolver o pacote:

### 1. Instalação limpa do Laravel:

A primeira coisa a ser feita é criar uma instalação limpa do Laravel:

```bash
$ composer create-project --prefer-dist laravel/laravel /caminho/do/projeto
$ cd /caminho/do/projeto
$ cp .env.example .env
$ php artisan key:generate
$ chmod 777 -Rf /caminho/do/projeto/bootstrap/cache
$ chmod 777 -Rf /caminho/do/projeto/storage
```

### 2. Diretório de desenvolvimento

Na raiz do projeto Laravel, crie o diretório 'packages'. Este diretório será usado para desenvolver pacotes:

```bash
$ mkdir /caminho/do/projeto/packages
```

### 3. Obtendo o pacote para desenvolvimento

No novo diretório de pacotes, é preciso criar a estrutura do pacote 'acl'. O formato deve ser '[vendor]/[pacote]', ou seja, a estrutura do pacote ficará assim '/plexi/access-control':

```bash
$ cd /caminho/do/projeto/packages
$mkdir -p plexi/access-control
```

No diretório 'acl', faça um clone do repositório:

```bash
$ cd /caminho/do/projeto/packages/plexi/access-control
$ git clone https://github.com/rpdesignerfly/acl.git .
```

### 4. Configurando o Laravel para usar o pacote

No arquivo "composer.json", abaixo da seção 'config', adicione 'minimum-stability' como 'dev' e o repositório apontando para o diretório './packages/plexi/access-control/'. 

> **Atenção:** 
> Não esqueça da barra (/) no final:

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

Com o repositório configurado, use normalmente o comando para instalação:

```bash
$ cd /caminho/do/projeto
$ composer require plexi/access-control
```



Em seguida, basta executar a instalação ou atualização do composer para que o pacote seja 
adicionado ao autoloader do composer:

```bash
$ cd /caminho/do/projeto
$ composer install
```

ou

```bash
$ cd /caminho/do/projeto
$ composer update
```

Para mais informações, leia:

* [Package Auto Discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518)
* [Custom Packages With Auto Discovery](https://medium.com/sureshvel/laravel-5-5-custom-packages-with-autodiscover-the-providers-5772c60d847e)

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)