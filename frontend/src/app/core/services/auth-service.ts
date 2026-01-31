import { DestroyRef, inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { timeout, retry, timer, throwError, catchError, finalize, tap, Subject, takeUntil } from 'rxjs';
import { AuthState, LoginData, LoginResponse } from '../models/auth.model';
import { LoggerService } from './logger-service';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private http = inject(HttpClient);
  private logger = inject(LoggerService);
  private destroyRef = inject(DestroyRef);

  public state = signal<AuthState>({
    isAuthenticated: false,
    user: null,
    loading: false
  });

  login(credentials: LoginData) {
    this.logger.debug('Iniciando sesión para:', credentials.email);
    this.state.update(s => ({ ...s, loading: true }));

    return this.http.post<LoginResponse>('/api/login-check', credentials).pipe(
      // 1. Timeout: Si back no responde en 8s, cancelamos.
      timeout(8000),

      // 2. Retry: Reintento exponencial solo si es un error de red o servidor (5xx)
      retry({
        count: 2,
        delay: (error, retryCount) => {
          // Si es un 401 (Credenciales mal), NO reintentamos
          if (error.status === 401 || error.status === 403) return throwError(() => error);

          this.logger.debug(`Reintento de login número ${retryCount}...`);
          return timer(retryCount * 1500); // 1.5s, luego 3s
        }
      }),

      // 3. Éxito: Guardamos en la Signal
      tap(response => {
        this.state.set({ isAuthenticated: true, user: response.user, loading: false });
        this.logger.debug('Login OK', response);
      }),

      // 4. Gestión de errores local
      catchError(error => {
        this.state.update(s => ({ ...s, loading: false }));
        this.logger.error('Fallo durante el Login', error);
        return throwError(() => error);
      }),

      // 5. Limpieza final (pase lo que pase)
      finalize(() => {
        this.state.update(s => ({ ...s, loading: false }));
      }),

      // 6. Auto-cancelación: Para evitar memory leaks
      takeUntilDestroyed(this.destroyRef)
    );
  }
}
