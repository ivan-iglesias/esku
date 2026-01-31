import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { timeout, retry, timer, throwError, catchError, TimeoutError } from 'rxjs';
import { LoggerService } from '../services/logger-service';

export const resilienceInterceptor: HttpInterceptorFn = (req, next) => {
  const logger = inject(LoggerService);

  const TIMEOUT_LIMIT = 8000;
  const RETRY_COUNT = 2;

  return next(req).pipe(
    // TIMEOUT
    timeout(TIMEOUT_LIMIT),

    // RETRY
    retry({
      count: RETRY_COUNT,
      delay: (error: any, retryCount: number) => {
        // Identificamos si es un Timeout
        const isTimeout = error instanceof TimeoutError || error.name === 'TimeoutError';

        // Extraemos el status (si existe)
        const status = error?.status;

        // Queremos reintentar SI es un Timeout o SI es un error de servidor (5xx)
        if (isTimeout || (status && status >= 500)) {
          logger.debug(`Reintentando por ${isTimeout ? 'Timeout' : 'Error ' + status} (${retryCount}/${RETRY_COUNT})`);
          return timer(retryCount * 1500);
        }

        return throwError(() => error);
      },
    }),

    // CATCH ERROR
    catchError((error: HttpErrorResponse | any) => {
      if (error.name === 'TimeoutError' || error instanceof TimeoutError) {
        logger.error(`Timeout en: ${req.url}`);
      }
      return throwError(() => error);
    })
  );
};
