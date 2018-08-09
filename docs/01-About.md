# 1. Access Control

## 1.1. Sobre o Access Control

O Access Control é uma biblioteca, desenvolvida para ser usada com o Laravel Framework, com o objetivo de prover as funcionalidades necessárias para o controle de acesso ([ACL](https://pt.wikipedia.org/wiki/Lista_de_controle_de_acesso)) dentro de qualquer projeto.

Ela surgiu da necessidade de modularizar estas funcionalidades, facilitando a implementação das mesmas em novos projetos desenvolvidos com Laravel.

![Lista de Usuários](https://github.com/rpdesignerfly/access-control/blob/master/docs/imgs/crud-users.png?raw=true)

Com **CRUD's funcionais e personalizáveis**, é simples configurar permissões baseadas em **Grupos de Acesso** ou exclusivamente para determinados usuários.

![Permissões de acesso](https://github.com/rpdesignerfly/access-control/blob/master/docs/imgs/crud-permissions.png?raw=true)

## 1.2. Recursos do Laravel

O sistema de permissões faz uso dos  [Gates](https://laravel.com/docs/5.6/authorization#writing-gates), um recurso poderoso que acompanha o Laravel.

Genericamente, o método ***can***, invocado através do facade ***Auth*** realiza as verificações sobre as funções e a habilidade do usuário logado, como pode ser observado no exemplo abaixo:

```php
\Auth::user()->can('users.create')
```

A invocação acima verifica se o usuário logado possui a habilidade ***create*** na função ***users***, retornando um valor booleano.

## 1.3. As versões da biblioteca

O método de versionamento utilizado para as evoluções da biblioteca seguem as regras da [Semantic Versioning](https://semver.org/lang/pt-BR/), uma especificação bastante utilizada na industria de Softwares, criada por Thom Preston Werner, criador do Gravatars e Co-Fundador do Github.

O formato das versões seguem a seguinte convenção:
```
X.Y.Z
```
Onde:

* X (major version): Muda quando temos incompatibilidade com versões anteriores.
* Y (minor version): Muda quando temos novas funcionalidades em nosso software.
* Z (patch version): Muda quando temos correções de bugs lançadas.

Explicando melhor:

**X**: é incrementado sempre que alterações **incompatíveis** com as versões anteriores da API forem implementadas. Por exemplo, sendo a versão atual 1.0.5, uma implementação precisou alterar campos no banco de dados, então a próxima versão será 2.0.0;

**Y**: é incrementado sempre que forem implementadas novas funcionalidades **compatíveis** e que não afetem o funcionamento normal da aplicação. Por exemplo, sendo a versão atual 1.9.5, uma nova funcionalidade foi adicionada, então a próxima versão será 1.10.0. Note que a versão do último número foi zerada para seguir a especificação da Semantic Versioning;

**Z**: é incrementado sempre que forem implementadas correções de falhas (bug fixes) que não afetem o funcionamento normal da aplicação. Por exemplo, sendo a versão atual 1.0.9, uma correção foi feita, gerando uma refatoração que otimizou o código, então a próxima versão será 1.0.10.

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
4. [Extras](04-Extras.md)
