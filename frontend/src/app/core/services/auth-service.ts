import { DestroyRef, inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { throwError, catchError, finalize, tap } from 'rxjs';
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
      tap(response => {
        this.state.set({ isAuthenticated: true, user: response.user, loading: false });
        this.logger.debug('Login OK', response);
      }),

      catchError(error => {
        this.state.update(s => ({ ...s, loading: false }));
        return throwError(() => error);
      }),

      finalize(() => {
        this.state.update(s => ({ ...s, loading: false }));
      }),

      takeUntilDestroyed(this.destroyRef)
    );
  }
}
