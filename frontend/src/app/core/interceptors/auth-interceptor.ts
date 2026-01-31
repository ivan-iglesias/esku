import { HttpInterceptorFn } from '@angular/common/http';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  // Clonamos la petición para añadir 'withCredentials'
  const authReq = req.clone({
    withCredentials: true
  });

  return next(authReq);
};
