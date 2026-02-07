import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { LoggerService } from '../services/logger-service';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const logger = inject(LoggerService);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      // Trazabilidad técnica, mensajes para usuario en el ErrorHandlerService
      const errorMessage = error?.error?.message || error.message;

      logger.error(`${error.status}: "${req.url}" | ${errorMessage}`);

      return throwError(() => error);
    }),
  );
};
