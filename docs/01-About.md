# Access Control

O Access Control foi criado para disponibilizar facilmente um gerenciador de [ACL](https://pt.wikipedia.org/wiki/Lista_de_controle_de_acesso) (Lista de Controle de Acesso) para projetos desenvolvidos com Laravel. 

![Lista de Usuários](https://github.com/rpdesignerfly/access-control/blob/master/docs/imgs/crud-users.png?raw=true)

Com **CRUD's funcionais e personalizáveis**, é simples configurar permissões baseadas em **Grupos de Acesso** ou exclusivamente para determinados usuários.

![Permissões de acesso](https://github.com/rpdesignerfly/access-control/blob/master/docs/imgs/crud-permissions.png?raw=true)

O sistema é construído sob a implementação de [Gates](https://laravel.com/docs/5.6/authorization#writing-gates) do Laravel. Genericamente, o método ***can***, invocado através do facade ***Auth*** realiza as verificações sobre as funções e a habilidade do usuário logado:

```php
\Auth::user()->can('users.create')
```

A invocação acima verifica se o usuário logado possui a habilidade ***create*** na função ***users***, retornando um valor booleano.

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)
  