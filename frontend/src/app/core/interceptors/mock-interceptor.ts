import { HttpInterceptorFn, HttpResponse, HttpErrorResponse } from '@angular/common/http';
import { throwError, delay, of } from 'rxjs';
import { MOCK_USERS } from '../mocks';

export const mockInterceptor: HttpInterceptorFn = (req, next) => {
  const { url, method } = req;

  // Mock para Login
  if (url.endsWith('/api/login-check') && method === 'POST') {
    return of(new HttpResponse({
      status: 200,
      body: { user: { id: 1, username: 'operario_bilbao', roles: ['ROLE_USER'] } }
    })).pipe(delay(800));
  }

  // if (url.endsWith('/api/login-check') && method === 'POST') {
  //   const errorResponse = new HttpErrorResponse({
  //     error: { message: 'Credenciales inválidas' },
  //     status: 400,
  //     statusText: 'Unauthorized',
  //     url: url
  //   });

  //   return throwError(() => errorResponse);
  // }

  // Mock para Listado de usuarios
  if (url.endsWith('/api/users') && method === 'GET') {
    return of(new HttpResponse({ status: 200, body: MOCK_USERS })).pipe(delay(500));
  }

  // Mock para detalle de un usuario específico
  if (url.match(/\/api\/users\/\d+$/) && method === 'GET') {
    return of(new HttpResponse({ status: 200, body: MOCK_USERS[0] })).pipe(delay(300));
  }

  // Si la URL no está mockeada, dejamos que la petición siga su curso hacia el backend real
  return next(req);
};
