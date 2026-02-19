import { computed, DestroyRef, inject, Injectable, signal } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Router } from '@angular/router';
import { throwError, catchError, tap, BehaviorSubject, filter, take, switchMap } from 'rxjs';

import { AuthState, LoginData, LoginResponse, User } from '../models/auth.model';
import { LoggerService } from './logger-service';
import { ErrorHandlerService } from './error-handler.service';
import { StorageService } from './storage-service';
import { environment } from '../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly http = inject(HttpClient);
  private readonly logger = inject(LoggerService);
  private readonly storage = inject(StorageService);
  private readonly errorService = inject(ErrorHandlerService);
  private readonly destroyRef = inject(DestroyRef);
  private readonly router = inject(Router);

  private readonly _state = signal<AuthState>({
    isAuthenticated: !!this.storage.get('user'),
    user: this.storage.get<User>('user'),
    loading: false,
    error: null,
  });

  private _isRefreshing = signal(false);
  private _refreshTokenSubject = new BehaviorSubject<string | null>(null);

  public readonly user = computed(() => this._state().user);
  public readonly username = computed(() => this._state().user?.username || 'Invitado');
  public readonly isAuthenticated = computed(() => this._state().isAuthenticated);
  public readonly loading = computed(() => this._state().loading);
  public readonly authError = computed(() => this._state().error);

  login(credentials: LoginData) {
    this.logger.debug('Iniciando sesión:', credentials.email);

    this._state.update((s) => ({ ...s, loading: true, error: null }));

    return this.http.post<LoginResponse>(`${environment.apiUrl}/auth/login`, credentials).pipe(
      tap((response) => {
        this.storage.set('at', response.accessToken);
        this.storage.set('user', response.user);

        this._state.set({
          isAuthenticated: true,
          user: response.user,
          loading: false,
          error: null,
        });
        this.logger.debug('Sesión iniciada correctamente');
      }),

      catchError((error: HttpErrorResponse) => {
        const errorMessage = this.errorService.getErrorMessage(error);
        this._state.update((s) => ({ ...s, loading: false, error: errorMessage }));
        return throwError(() => error);
      }),

      takeUntilDestroyed(this.destroyRef)
    );
  }

  refreshToken() {
    if (this._isRefreshing()) {
      return this._refreshTokenSubject.pipe(
        filter((token) => token !== null),
        take(1)
      );
    }

    this._isRefreshing.set(true);
    this._refreshTokenSubject.next(null);

    return this.http
      .post<any>(`${environment.apiUrl}/auth/refresh`, {}, { withCredentials: true })
      .pipe(
        tap((response) => {
          this.storage.set('at', response.accessToken);
          this._isRefreshing.set(false);
          this._refreshTokenSubject.next(response);
        }),
        catchError((err) => {
          this._isRefreshing.set(false);
          this._refreshTokenSubject.next(null);
          this.logout();
          return throwError(() => err);
        })
      );
  }

  logout() {
    this.storage.clear();
    this._state.set({ isAuthenticated: false, user: null, loading: false, error: null });
    this.logger.debug('Sesión cerrada y storage limpio');
    this.router.navigate(['/login']);
  }
}
