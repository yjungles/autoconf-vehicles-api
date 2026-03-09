
# AutoConf Vehicles API

API REST desenvolvida com **Laravel 12** para gerenciamento de veículos.

## Sobre o projeto

A **AUTOCONF Vehicles API** é uma API construída em Laravel para permitir:

- Cadastro e autenticação de usuários baseada em sessão/cookie com **Sanctum para SPA**
- Consulta e gerenciamento de veículos
- Upload e gerenciamento de imagens de veículos

## Stack utilizada

#### Backend
- **PHP 8.2+**
- **Laravel 12**
- **Laravel Sanctum**
- **MySQL**
- **Scramble** para documentação OpenAPI

#### Testes
- **Pest**

## Requisitos

Antes de iniciar, você precisa ter instalado:

- **PHP 8.2+**
- **Composer**
- **Node.js**
- **npm / pnpm**
- **MySQL / MariaDB**
- Extensões PHP normalmente utilizadas pelo Laravel:
    - `pdo_mysql`
    - `mbstring`
    - `openssl`
    - `tokenizer`
    - `xml`
    - `ctype`
    - `json`
    - `fileinfo`


## Instalação

Clone o repositório:

```bash
git clone https://github.com/yjungles/autoconf-vehicles-api.git autoconf-vehicles-api
``` 

Instale as dependências do PHP:
```bash
composer install
``` 

Instale as dependências do frontend:
```bash
npm install
``` 
Copie o arquivo de ambiente:
```bash
cp .env.example .env
``` 

Gere a chave da aplicação:
```bash
php artisan key:generate
``` 

---

## Configuração do ambiente

Configure o arquivo `.env` com os dados do seu ambiente local.

### Exemplo de configuração mínima

#### Variáveis importantes deste projeto

Além das variáveis padrão do Laravel, este projeto usa configurações específicas para imagens de veículos:

```env
VEHICLE_IMAGES_DISK=public 
VEHICLE_IMAGES_DIRECTORY=vehicles 
VEHICLE_IMAGES_MAX_SIZE_KB=5120 
VEHICLE_IMAGES_DEFAULT_IMAGE_PATH=default.png 
VEHICLE_IMAGES_ALLOWED_EXTENSIONS="jpg,jpeg,png,bmp"

SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
``` 

### Observações importantes

- `SANCTUM_STATEFUL_DOMAINS` deve conter os domínios/hosts da sua aplicação SPA.
- Como a autenticação SPA do Sanctum usa **cookies + sessão**, a configuração correta de domínio e CORS é essencial.
- `VEHICLE_IMAGES_DEFAULT_IMAGE_PATH` deve apontar para a imagem padrão utilizada nas seeders e factories. No exemplo ela fica em `/public/default.png` dentro da storage
---

## Banco de dados

Execute as migrations:
```bash
php artisan migrate
``` 
Para recriar e popular com seeders:
```bash
php artisan migrate:fresh --seed
``` 


## Storage e arquivos públicos

Este projeto usa o disco `public` para armazenar imagens de veículos.

Crie o link simbólico do storage:
```
php artisan storage:link
``` 

## Executando o projeto

Servidor Laravel:
```
php artisan serve
``` 

## Seeders e dados iniciais

Para popular o banco com dados iniciais:

```
php artisan db:seed
``` 

Ou:
```
bash php artisan migrate:fresh --seed
``` 


O projeto possui seeders para gerar dados de usuários e veículos de exemplo para ambiente local.

> Recomenda-se conferir os seeders antes de usar em ambientes compartilhados ou de homologação.

#### Usuários de exemplo
Administrador:
```
admin@example.com
senha: password
```
Usuário:
```
test@example.com
senha: password
```


## Autenticação com Sanctum SPA

Este projeto utiliza **Laravel Sanctum no modo SPA Authentication**, ou seja:

- Autenticação baseada em **sessão**
- Uso de **cookies**
- Proteção por **CSRF**

### Fluxo de autenticação

#### 1. Obter cookie CSRF

Antes de fazer login, a SPA deve chamar:
```
http GET /api/csrf-cookie
``` 
#### 2. Fazer login
Envie as credenciais para:
```http
POST /api/auth/login
``` 

Exemplo de payload:
```json
{ "email": "<EMAIL_DO_USUARIO>", "password": "<SENHA_DO_USUARIO>" }
``` 

Se autenticado com sucesso, a sessão será iniciada e o navegador passará a enviar automaticamente os cookies nas próximas requisições.

#### 3. Consultar usuário autenticado
```http
GET /api/auth/me
``` 

#### 4. Fazer logout
```http
POST /api/auth/logout
``` 

### Requisitos para o Sanctum SPA

- o frontend deve enviar requisições com `withCredentials: true`
- `SANCTUM_STATEFUL_DOMAINS` deve incluir o domínio/porta da SPA
- `SESSION_DOMAIN`, `APP_URL` e CORS devem estar coerentes com seu ambiente
- o fluxo deve começar obtendo o cookie CSRF
---

## Documentação da API

A documentação da API é gerada com **Scramble**.

### Acessar a documentação
Com a aplicação em execução, acesse:
```
/docs/api
``` 

Exemplo local:
```
http://localhost:8000/docs/api
``` 

### O que a documentação permite

- rotas disponíveis
- parâmetros
- payloads
- respostas
- schemas
- possibilidade de testar endpoints pela interface, quando aplicável

#### Observação
A documentação é gerada com base nas rotas da API e na estrutura da aplicação. Sempre que novos endpoints forem adicionados, a documentação pode ser atualizada automaticamente conforme o uso do Scramble no projeto.

## Principais rotas

### Autenticação

| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/csrf-cookie` | Inicializa cookies/CSRF para autenticação SPA |
| POST | `/api/auth/register` | Cadastro de usuário |
| POST | `/api/auth/login` | Login |
| POST | `/api/auth/logout` | Logout do usuário autenticado |
| GET | `/api/auth/me` | Retorna o usuário autenticado |

### Veículos

> As rotas abaixo exigem autenticação com `auth:sanctum`.

| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/vehicles` | Lista veículos |
| POST | `/api/vehicles` | Cria veículo |
| GET | `/api/vehicles/{id}` | Exibe veículo |
| PUT/PATCH | `/api/vehicles/{id}` | Atualiza veículo |
| DELETE | `/api/vehicles/{id}` | Remove veículo |

### Imagens de veículos

| Método | Rota | Descrição |
|---|---|---|
| POST | `/api/vehicles/{vehicleId}/images` | Adiciona imagem ao veículo |
| PATCH | `/api/vehicles/{vehicleId}/images/{imageId}/cover` | Define imagem como capa |
| DELETE | `/api/vehicles/{vehicleId}/images/{imageId}` | Remove imagem |

> Para detalhes completos de payload e resposta, consulte `/docs/api`.

## Testes

Para executar os testes:
```bash
php artisan test
``` 

## Boas práticas para consumo da API

- sempre obtenha o cookie CSRF antes do login em SPA
- envie credenciais com `withCredentials: true`
- use a documentação em `/docs/api` para validar formatos de entrada e saída
- em ambiente local, verifique se `APP_URL`, `SESSION_DOMAIN` e `SANCTUM_STATEFUL_DOMAINS` estão compatíveis
- execute `php artisan storage:link` antes de testar upload e acesso a arquivos públicos
- execute os seeders para ter massa de dados inicial de desenvolvimento


## Comandos úteis

```bash
composer install 
npm install 
cp .env.example .env 
php artisan key:generate 
php artisan migrate 
php artisan storage:link 
php artisan db:seed 
php artisan test
``` 
