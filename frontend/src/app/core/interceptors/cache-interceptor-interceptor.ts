import { HttpInterceptorFn, HttpResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { of, tap } from 'rxjs';
import { CacheService } from '../services/cache-service';
import { BYPASS_CACHE } from '../http/http-context';

export const cacheInterceptor: HttpInterceptorFn = (req, next) => {
  // Solo GET y si no se pide bypass
  if (req.method !== 'GET' || req.context.get(BYPASS_CACHE)) {
    return next(req);
  }

  const cache = inject(CacheService);
  const cachedResponse = cache.get(req.urlWithParams);

  if (cachedResponse) {
    return of(cachedResponse);
  }

  return next(req).pipe(
    tap((event) => {
      if (event instanceof HttpResponse && event.status === 200) {
        cache.put(req.urlWithParams, event);
      }
    }),
  );
};
