# PHP-QA - CRUD de Usuarios con DDD + Hexagonal + CQRS

Proyecto educativo en PHP para practicar:

- Arquitectura Hexagonal (Ports & Adapters)
- Domain Driven Design (DDD)
- CQRS
- Principios SOLID
- Clean Code

## Estructura

```text
public/
	index.php                        # Entrypoint y routing (?route=)

src/
	bootstrap.php                    # Wiring de dependencias
	Domain/
		User/
			Entity/
			Enum/
			Exception/
			ValueObject/
	Application/
		User/
			Command/                     # CQRS Commands
			Query/                       # CQRS Queries
			Service/                     # Casos de uso
			Port/                        # Puertos (interfaces)
			Mapper/                      # Mapper de respuesta
	Infrastructure/
		Persistence/PDO/
		User/Mapper/
		User/Repository/

database/
	schema.sql                       # Script SQL
```

## Requisitos

- PHP 8.1+
- MySQL 8+

## Base de datos

1. Ejecuta el script:

```sql
database/schema.sql
```

2. Configura variables de entorno (tomando `.env.example` como base):

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=php_qa
DB_USER=root
DB_PASSWORD=
```

## Ejecutar

Desde la raíz del proyecto:

```bash
php -S localhost:8000 -t public
```

## Pruebas rapidas

Desde la raiz del proyecto:

```bash
php test/domain_test.php
php test/user_flow_test.php
```

Estas pruebas validan:

- Value Objects del dominio
- Flujo completo CQRS (crear, listar, actualizar, obtener por ID, login y eliminar)

## Pruebas con PHPUnit

1. Instala dependencias de desarrollo:

```bash
composer install
```

2. Ejecuta la suite:

```bash
composer test
```

Archivos de prueba incluidos:

- `test/Unit/Application/User/Service/CreateUserServiceTest.php`
	- Caso valido: crea usuario, guarda en repositorio y envia correo.
	- Casos invalidos: email duplicado, role invalido y status invalido.
	- Usa mocks de `UserRepositoryPort` y `EmailServicePort`.

- `test/Unit/Application/User/Service/LoginUserServiceTest.php`
	- Caso valido: login exitoso con credenciales correctas.
	- Casos invalidos: usuario no existe y password incorrecto.
	- Usa mock de `UserRepositoryPort`.

- `test/Unit/Domain/User/ValueObject/UserValueObjectsTest.php`
	- Casos validos e invalidos para `UserId`, `UserName`, `UserEmail` y `UserPassword`.
	- Verifica manejo de excepciones de dominio en validaciones.

## Rutas disponibles

### 1) Crear usuario

- Método: `POST`
- URL: `http://localhost:8000/index.php?route=create-user`
- Body (JSON o form-data):

```json
{
	"name": "Ada Lovelace",
	"email": "ada@example.com",
	"password": "StrongPass1",
	"role": "user",
	"status": "active"
}
```

### 2) Actualizar usuario

- Método: `POST`
- URL: `http://localhost:8000/index.php?route=update-user`
- Body:

```json
{
	"id": "user-id",
	"name": "Ada L.",
	"email": "ada.new@example.com"
}
```

### 3) Eliminar usuario

- Método: `POST`
- URL: `http://localhost:8000/index.php?route=delete-user`
- Body:

```json
{
	"id": "user-id"
}
```

### 4) Obtener usuario por ID

- Método: `GET`
- URL: `http://localhost:8000/index.php?route=get-user&id=user-id`

### 5) Listar usuarios

- Método: `GET`
- URL: `http://localhost:8000/index.php?route=list-users&limit=50&offset=0`

### 6) Login

- Método: `POST`
- URL: `http://localhost:8000/index.php?route=login`
- Body:

```json
{
	"email": "ada@example.com",
	"password": "StrongPass1"
}
```

## Notas de diseño

- El dominio no depende de infraestructura.
- Los casos de uso dependen de puertos, no de implementaciones concretas.
- `PdoUserRepository` es un adaptador de infraestructura.
- Se usan `prepared statements` para consultas SQL seguras.
- `EmailServicePort` define el contrato de email en Aplicacion.
- `Infrastructure/Email/EmailService` implementa el envio con `mail()` y fallback a log en `var/email.log`.
- `CreateUserService` envia correo de bienvenida despues de guardar el usuario.