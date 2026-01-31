import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { catchError, throwError } from 'rxjs';

// Si tenemos un servicio para mostrar notificaciones (Toasts)
// import { NotificationService } from '@core/services/notification.service';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  // const notify = inject(NotificationService);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      let errorMessage = 'Ha ocurrido un error inesperado';

      if (error.status === 0) {
        errorMessage = 'Sin conexión. Esku funcionará en modo offline.';
        // Aquí podrías disparar un evento global de "Modo Offline"
      } else if (error.status >= 400 && error.status < 500) {
        errorMessage = error.error?.message || 'Datos incorrectos';
      } else if (error.status >= 500) {
        errorMessage = 'Error en el servidor. Contacte con soporte.';
      }

      console.error(`[ERROR ${error.status}]: ${errorMessage}`);
      // notify.show(errorMessage, 'error');

      return throwError(() => error);
    })
  );
};
