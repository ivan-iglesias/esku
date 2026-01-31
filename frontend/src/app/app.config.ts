import { ApplicationConfig, provideBrowserGlobalErrorListeners } from '@angular/core';
import { provideRouter } from '@angular/router';

import { routes } from './app.routes';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { errorInterceptor } from './core/interceptors/error-interceptor';
import { mockInterceptor } from './core/interceptors/mock-interceptor';
import { environment } from '../environments/environment';
import { authInterceptor } from './core/interceptors/auth-interceptor';
import { resilienceInterceptor } from './core/interceptors/resilience-interceptor';

const isDev = !environment.production;

export const appConfig: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideRouter(routes),
    provideHttpClient(
      withInterceptors([
        resilienceInterceptor,
        authInterceptor,
        errorInterceptor,
        ...(isDev ? [mockInterceptor] : []),
      ])
    )
  ]
};
