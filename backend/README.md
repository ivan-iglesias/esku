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
