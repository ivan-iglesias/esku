import { inject } from '@angular/core';
import { Router, CanActivateFn } from '@angular/router';
import { AuthService } from '../services/auth-service';

export const authGuard: CanActivateFn = () => {
  const authService = inject(AuthService);
  const router = inject(Router);

  // Accedemos al estado de la Signal
  if (authService.state().isAuthenticated) {
    return true;
  }

  // Si no está autenticado, al login
  return router.parseUrl('/login');
};
