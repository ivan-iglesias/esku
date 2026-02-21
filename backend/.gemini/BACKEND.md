# API Architecture Rules

Este documento define los estándares de desarrollo para el backend (Symfony 8).

## 1. Estructura de Respuesta (ApiResponse)
Todas las respuestas deben usar el objeto `App\Shared\Infrastructure\Response\ApiResponse`.
**Estructura JSON obligatoria:**
- `code`: (string) 'SUCCESS' o código de error de negocio.
- `message`: (string) Mensaje legible.
- `data`: (mixed|null) Datos de la respuesta o errores de validación.
- `correlation_id`: (uuid) Generado automáticamente para trazabilidad.

## 2. Controladores
- Todos deben heredar de `App\Shared\Infrastructure\Controller\BaseApiController`.
- **POST/PUT**: Usar `$this->handleInput(Request $request, DTOClass::class, $callback)`.
- **GET/DELETE**: Usar `$this->handleSimpleAction($callback)`.
- No usar `try-catch` dentro de los controladores; la lógica de captura reside en `BaseApiController::runSafe`.

## 3. Gestión de Errores
- **Errores de Negocio**: Lanzar `App\Shared\Domain\Exception\BusinessException`.
- **Trazabilidad**: El `CorrelationIdListener` inicializa el ID en cada petición. Está integrado en logs vía Monolog.

## 4. Convenciones
- Arquitectura: Capas (Domain, Application, Infrastructure).
- Fechas: Siempre en formato ISO 8601 (UTC).
- Tipado: Strict typing en todos los archivos (`declare(strict_types=1);`).
