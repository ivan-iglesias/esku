# Manual de Estilo y Arquitectura

## Identidad
- **Nombre del Proyecto:** Esku (Euskera: "Mano").
- **Eslogan:** "Tu stock, a mano".
- **Concepto:** Control total y agilidad en la palma de la mano.

## Perfil del Proyecto
- **SaaS:** Gestión de inventario universal (Multisector: Retail, Hostelería, Almacenes).
- **Backend:** Symfony 8 (PHP 8.5), PostgreSQL, Docker.
- **Frontend:** Angular v21, PWA.
- **Enfoque de Diseño:** Mobile-first (Optimizado para operarios en movimiento y uso con una mano).

## Reglas de Estilo CSS (CRÍTICO)
- **Metodología:** BEM (Block Element Modifier).
- **Prohibido:** No usar Tailwind ni librerías de utilidad.
- **Sin Anidamiento:** Escribir clases completas (ej. `.inventory-card__status`).
- **Nomenclatura:** Clases planas y descriptivas.

## Arquitectura Técnica
- **Offline-First:** Uso de IndexedDB (Dexie.js) para escaneo y conteo en zonas sin cobertura.
- **Sincronización:** Background Sync para movimientos de stock diferidos.
- **PHP 8.5:** Tipos estrictos y atributos modernos.

## Diccionario de Datos (Universal)
- **Product:** Ficha técnica del artículo.
- **Barcode:** Soporte para múltiples códigos (EAN, UPC, QR).
- **Movement:** Trazabilidad (Entrada, Salida, Ajuste de Inventario, Merma).
- **Location:** Almacén, estantería o pasillo.

## Idioma
- **Código:** Inglés.
- **Interfaz/Comunicación:** Español.
