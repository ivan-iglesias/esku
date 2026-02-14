## Crear una nueva migración

Detectar cambios y generar el archivo de migración

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Borrón y cuenta nueva

```
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:schema:validate
```

## Crear usuario de prueba

```
php bin/console app:create-user
```

## Dependencias

**symfony/security-bundle**

Proporciona herramientas para el hash de contraseñas (encriptarlas), el manejo de roles (ROLE_ADMIN, ROLE_DRIVER) y el control de acceso a las rutas.

**lexik/jwt-authentication-bundle**

Implementa el estándar JWT (JSON Web Token). Cuando un usuario se loguea con éxito, este bundle genera una cadena de texto larga y firmada (el token).

**doctrine/orm y doctrine/doctrine-bundle**

Te permite hablar con la base de datos usando objetos de PHP en lugar de escribir SQL a mano constantemente.

**psr/log**

PSR significa PHP Standard Recommendation. Este paquete define una interfaz común para que todos los sistemas de logging (como Monolog, que viene con Symfony) hablen el mismo idioma.

**nelmio/api-doc-bundle**

Este paquete lee tu código (tus controladores, tus entidades y los atributos que añadimos como #[OA\Post]) y lo convierte en un archivo de especificación OpenAPI.

**symfony/twig-bundle**

Twig es un lenguaje que permite generar HTML de forma dinámica. Aunque tu API solo devuelva JSON, Symfony necesita Twig para "dibujar" páginas web internas.
