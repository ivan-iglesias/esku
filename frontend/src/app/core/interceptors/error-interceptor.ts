import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { LoggerService } from '../services/logger-service';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  const logger = inject(LoggerService);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      let errorMessage = 'Ha ocurrido un error inesperado';

      if (error.status === 0) {
        errorMessage = 'Sin conexión. Esku funcionará en modo offline.';
      } else if (error.status >= 400 && error.status < 500) {
        errorMessage = error.error?.message || 'Datos incorrectos';
      } else if (error.status >= 500) {
        errorMessage = 'Error en el servidor. Contacte con soporte.';
      }

      logger.error(`${error.status}: ${errorMessage}`);

      return throwError(() => error);
    })
  );
};
