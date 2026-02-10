import { DestroyRef, inject, Injectable, signal } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { throwError, catchError, tap } from 'rxjs';
import { AuthState, LoginData, LoginResponse, User } from '../models/auth.model';
import { LoggerService } from './logger-service';
import { ErrorHandlerService } from './error-handler.service';
import { StorageService } from './storage-service';
import { environment } from '../../../environments/environment';
import { Router } from '@angular/router';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private http = inject(HttpClient);
  private logger = inject(LoggerService);
  private storage = inject(StorageService);
  private errorService = inject(ErrorHandlerService);
  private destroyRef = inject(DestroyRef);
  private router = inject(Router);

  public state = signal<AuthState>({
    isAuthenticated: !!this.storage.get('user'),
    user: this.storage.get<User>('user'),
    loading: false,
    error: null,
  });

  login(credentials: LoginData) {
    this.logger.debug('Iniciando sesión:', credentials.email);

    this.state.update((s) => ({ ...s, loading: true, error: null }));

    return this.http.post<LoginResponse>(`${environment.apiUrl}/auth/login`, credentials).pipe(
      tap((response) => {
        this.storage.set('at', response.accessToken);
        this.storage.set('user', response.user);

        this.state.set({ isAuthenticated: true, user: response.user, loading: false, error: null });
        this.logger.debug('Sesión iniciada');
      }),

      catchError((error: HttpErrorResponse) => {
        const errorMessage = this.errorService.getErrorMessage(error);
        this.state.update((s) => ({ ...s, loading: false, error: errorMessage }));
        return throwError(() => error);
      }),

      takeUntilDestroyed(this.destroyRef)
    );
  }

  refreshToken() {
    return this.http
      .post<any>(`${environment.apiUrl}/auth/refresh`, {}, { withCredentials: true })
      .pipe(
        tap((res) => {
          this.storage.set('at', res.accessToken);
        })
      );
  }

  logout() {
    this.storage.clear();
    this.state.set({ isAuthenticated: false, user: null, loading: false, error: null });
    this.logger.debug('Sesión cerrada y storage limpio');
    this.router.navigate(['/login']);
  }
}
