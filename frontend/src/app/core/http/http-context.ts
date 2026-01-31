import { HttpContextToken } from '@angular/common/http';

/**
 * si es true, fuerza una petición de red en CacheInterceptor.
 */
export const BYPASS_CACHE = new HttpContextToken<boolean>(() => false);

/**
 * Descartar timeout en peticiones que tardan mucho.
 */
export const IS_LONG_RUNNING = new HttpContextToken<boolean>(() => false);
