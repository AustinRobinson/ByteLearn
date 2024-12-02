import { inject } from '@angular/core';
import { HttpEvent, HttpHandlerFn, HttpRequest } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from '../../services/auth/auth.service';

export function authInterceptor(req: HttpRequest<unknown>, next: HttpHandlerFn): Observable<HttpEvent<unknown>> {
  const authService = inject(AuthService);

  // get the access token
  const accessToken = authService?.accessToken;

  // if the access token is set, add it to the request headers and pass the request
  if (accessToken) {
    console.log('Adding access token to request:', accessToken);
    return next(req.clone({
      setHeaders: {
        Authorization: `Bearer ${accessToken}`
      }
    }));
  }

  // pass the request to the next interceptor
  return next(req);
};
