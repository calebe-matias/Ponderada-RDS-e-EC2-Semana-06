## `README.md` – Projeto de Gestão de Veículos em Concessionária 🚗

### Visão Geral

Este é um projeto simples em PHP que permite o gerenciamento de veículos de uma concessionária usando um banco de dados PostgreSQL. A aplicação roda em um servidor Apache (EC2 AWS) e possibilita realizar operações **CRUD completas (Create, Read, Update, Delete)** em um catálogo de veículos.

---

### Estrutura de Arquivos

```
/var/www/
├── html/
│   ├── Concessionaria.php      # Página principal da aplicação (CRUD de veículos)
│   ├── SamplePage.php          # Exemplo didático de conexão e manipulação de dados
│
└── inc/
    └── dbinfo.inc              # Arquivo com credenciais de conexão ao banco (não acessível via web)
```

---

### Arquivos principais

#### `Concessionaria.php`

Arquivo principal do sistema. Possui:

- Formulário para cadastrar veículos (marca, modelo, ano, preço e status)
- Listagem de todos os veículos cadastrados
- Função de edição e exclusão de veículos
- Criação automática da tabela `CARS` caso não exista

#### `SamplePage.php`

Página de exemplo que realiza operações básicas de criação e listagem dos registros, mas voltado para uma tabela de `EMPLOYEES`. Foi montada com base no tutorial do [site da AWS](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/TUT_WebAppWithRDS.html).

#### `dbinfo.inc`

Arquivo externo contendo as constantes de configuração para conexão com o banco de dados PostgreSQL:

```php
define('DB_SERVER', 'tutorial-db-instance.cfw0ywqwoc54.us-east-2.rds.amazonaws.com');
define('DB_USERNAME', 'tutorial_user');
define('DB_PASSWORD', '12345678');
define('DB_DATABASE', 'sample');
```

Este arquivo, em geral, não deve ser exposto ao navegador ou em repositorios públicos, porém neste caso irei encerras as intâncias após a entrega da ponderada.

---

### Tecnologias Utilizadas

- PHP 8.x
- PostgreSQL (Amazon RDS)
- Apache 2 (Amazon EC2)
- HTML + CSS (básico)

---

### Como usar

1. Faça deploy dos arquivos no diretório `/var/www/html` da sua instância EC2.
2. Edite o arquivo `dbinfo.inc` com os dados corretos do seu banco RDS.
3. Acesse pelo navegador:

   ```
   http://<seu-endereço-ip-ou-dns>/Concessionaria.php
   ```

4. Cadastre, edite e exclua veículos conforme desejar.