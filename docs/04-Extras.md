# 5. Extras

## Sumário

1. [Sobre](00-Home.md)
2. [Instalação](01-Installation.md)
3. [Como Usar](02-Usage.md)
4. [Exemplos](03-Examples.md)
5. [Extras](04-Extras.md)

## Para desenvolver o pacote:

### 1. Instalação limpa do Laravel:

A primeira coisa a ser feita é criar uma instalação limpa do Laravel:

```
$ composer create-project --prefer-dist laravel/laravel /caminho/do/projeto
$ cd /caminho/do/projeto
$ cp .env.example .env
$ php artisan key:generate
$ chmod 777 -Rf /caminho/do/projeto/bootstrap/cache
$ chmod 777 -Rf /caminho/do/projeto/storage
```

### 2. Diretório de desenvolvimento

Na raiz do projeto Laravel, crie o diretório 'packages'. Este diretório será usado para desenvolver pacotes:

```
$ mkdir /caminho/do/projeto/packages
```

### 3. Obtendo o pacote para desenvolvimento

No novo diretório de pacotes, é preciso criar a estrutura do pacote 'laravel-old-extened'. O formato deve ser '[vendor]/[pacote]', ou seja, a estrutura do pacote ficará assim '/plexi/laracl':

```
$ cd /caminho/do/projeto/packages
$mkdir -p plexi/laracl
```

No diretório 'laracl', faça um clone do repositório:

```
$ cd /caminho/do/projeto/packages/plexi/laracl
$ git clone https://github.com/rpdesignerfly/laracl.git .
```

### 4. Configurando o Laravel para usar o pacote

No arquivo "composer.json", abaixo da seção 'config', adicione 'minimum-stability' como 'dev' e o repositório apontando para o diretório './packages/plexi/laracl/'. 

> **Atenção:** 
> Não esqueça da barra (/) no final:

```
{
    "name": "laravel/laravel",
    "description": "The Laravel Framework.",

    ...


    "config": {
        ...
    },

    "minimum-stability" : "dev",
    "repositories": [
        {"type": "path", "url": "./packages/plexi/laracl/"}
    ]

}
```

Com o repositório configurado, use normalmente o comando para instalação:

```
$ cd /caminho/do/projeto
$ composer require plexi/laracl
```



Em seguida, basta executar a instalação ou atualização do composer para que o pacote seja 
adicionado ao autoloader do composer:

```
$ cd /caminho/do/projeto
$ composer install
```

ou

```
$ cd /caminho/do/projeto
$ composer update
```

Para mais informações, leia:

* [Package Auto Discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518)
* [Custom Packages With Auto Discovery](https://medium.com/sureshvel/laravel-5-5-custom-packages-with-autodiscover-the-providers-5772c60d847e)