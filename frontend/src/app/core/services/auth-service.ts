import { DestroyRef, inject, Injectable, signal } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { throwError, catchError, finalize, tap } from 'rxjs';
import { AuthState, LoginData, LoginResponse } from '../models/auth.model';
import { LoggerService } from './logger-service';
import { ErrorHandlerService } from './error-handler.service';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private http = inject(HttpClient);
  private logger = inject(LoggerService);
  private errorService = inject(ErrorHandlerService);
  private destroyRef = inject(DestroyRef);

  public state = signal<AuthState>({
    isAuthenticated: false,
    user: null,
    loading: false,
    error: null
  });

  login(credentials: LoginData) {
    this.logger.debug('Iniciando sesión:', credentials.email);

    this.state.update(s => ({ ...s, loading: true, error: null }));

    return this.http.post<LoginResponse>('/api/login-check', credentials).pipe(
      tap(response => {
        this.state.set({ isAuthenticated: true, user: response.user, loading: false, error: null });
        this.logger.debug('Sesión iniciada');
      }),

      catchError((error: HttpErrorResponse) => {
        const errorMessage = this.errorService.getErrorMessage(error);
        this.state.update(s => ({ ...s, loading: false, error: errorMessage  }));
        return throwError(() => error);
      }),

      takeUntilDestroyed(this.destroyRef)
    );
  }

  logout() {
    this.state.set({ isAuthenticated: false, user: null, loading: false, error: null });
    this.logger.debug('Sesión cerrada');
  }
}
