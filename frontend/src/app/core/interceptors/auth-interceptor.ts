import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth-service';
import { StorageService } from '../services/storage-service';
import { catchError, switchMap, throwError } from 'rxjs';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const storage = inject(StorageService);

  // 1. Obtenemos el token actual del storage
  const accessToken = storage.get<string>('at');
  let authReq = req;

  if (accessToken) {
    authReq = req.clone({
      setHeaders: { Authorization: `Bearer ${accessToken}` },
    });
  }

  return next(authReq).pipe(
    catchError((error: HttpErrorResponse) => {
      // 2. Si es 401 y no es login ni el propio refresh
      if (error.status === 401 && !req.url.includes('auth/login') && !req.url.includes('auth/refresh')) {
        return authService.refreshToken().pipe(
          switchMap((response) => {
            // 3. Reintentamos con el token que viene fresco del servicio
            return next(req.clone({
              setHeaders: { Authorization: `Bearer ${response.accessToken}` },
            }));
          }),
          // 4. Si el refresh falla, el servicio ya hizo logout, pero aquí matamos la petición
          catchError((refreshErr) => throwError(() => refreshErr))
        );
      }
      return throwError(() => error);
    })
  );
};
