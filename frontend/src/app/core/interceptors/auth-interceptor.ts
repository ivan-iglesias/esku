import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth-service';
import { StorageService } from '../services/storage-service';
import { catchError, switchMap, throwError } from 'rxjs';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const storage = inject(StorageService);

  const accessToken = storage.get<string>('at');

  let authReq = req;

  if (accessToken) {
    authReq = req.clone({
      setHeaders: { Authorization: `Bearer ${accessToken}` },
    });
  }

  return next(authReq).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401 && !req.url.includes('auth/login')) {
        return authService.refreshToken().pipe(
          switchMap((response) => {
            // Reintentamos la petición original con el nuevo token
            const retryReq = req.clone({
              setHeaders: { Authorization: `Bearer ${response.accessToken}` },
            });
            return next(retryReq);
          }),

          catchError((err) => {
            // Si el refresh también falla (401 o 403), el usuario debe loguearse de nuevo
            authService.logout();
            return throwError(() => err);
          })
        );
      }
      return throwError(() => error);
    })
  );
};
