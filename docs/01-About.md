# Laracl

O Laracl foi desenvolvido para disponibilizar facilmente um gerenciador de [ACL](https://pt.wikipedia.org/wiki/Lista_de_controle_de_acesso) (Lista de Controle de Acesso) para um projeto Laravel. 

Com CRUD's funcionais e personalizáveis, é simples configurar permissões baseadas em Grupos de Acesso ou exclusivamente para determinados usuários.

O sistema é construído sob a implementação de [Gates](https://laravel.com/docs/5.6/authorization#writing-gates) do Laravel, que monitoram o conteúdo com base na configuração.

Genericamente, o método ***can***, invocado através do facade ***Auth*** realiza as verificações sobre a função e a habilidade do usuário logado:

```php
\Auth::user()->can('users.create')
```

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)
  