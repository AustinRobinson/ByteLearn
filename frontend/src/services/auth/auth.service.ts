import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, tap, throwError } from 'rxjs';
import { LoginFormData } from '../../pages/login/login.component';
import { environment } from '../../environments/environment';

interface LoginResponse {
  accessToken: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private accessToken: string | null = null;

  constructor(private http: HttpClient) {

  }

  login(formData: LoginFormData) {
    return this.http.post<LoginResponse>(`${environment.apiBaseUrl}/login`, formData).pipe(
      tap((response) => {
        this.accessToken = response.accessToken;
      }),
    );
  }
}
