import { inject } from '@angular/core';
import { AuthService } from '../../services/auth/auth.service';
import { catchError, map, of, tap } from 'rxjs';
import { Router } from '@angular/router';

export function authGuard() {
  const authService = inject(AuthService);
  const router = inject(Router);

  // check if access token is set
  if (authService.isAuthenticated()) {
    return true;
  }

  router.navigate(['/login']);
  return false;
}
