import { APP_INITIALIZER, ApplicationConfig, inject, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';

import { routes } from './app.routes';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { authInterceptor } from './interceptors/auth/auth.interceptor';
import { catchError, firstValueFrom, Observable, of } from 'rxjs';
import { AuthService, LoginResponse } from './services/auth/auth.service';

function appInitializerFactory(authService: AuthService): () => Promise<void> {
  return () =>
    new Promise<void>((resolve) => {
      authService.refresh().subscribe({
        next: () => resolve(),
        error: () => resolve(),
      });
    });
}

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
    provideHttpClient(
      withInterceptors([authInterceptor])
    ),
    {
      provide: APP_INITIALIZER,
      useFactory: appInitializerFactory,
      deps: [AuthService],
      multi: true
    }
  ]
};
