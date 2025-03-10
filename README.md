# EB Cursos Platform

Plataforma institucional e LMS da EB Cursos, desenvolvida com PHP 8.1 e MySQL.

## Requisitos

- PHP 8.1+
- MySQL 5.7+
- Composer

## Estrutura

- `/admin` - Área administrativa
- `/alunos` - Área do aluno
- `/assets` - Arquivos estáticos (CSS, JS, imagens)
- `/includes` - Classes e funções compartilhadas
- `/config` - Configurações do sistema

## Instalação

1. Clone o repositório
2. Configure o arquivo `config/database.php` com suas credenciais
3. Importe o arquivo `database.sql` no seu MySQL
4. Execute `composer install`
5. Configure o servidor web para apontar para a pasta `public`
