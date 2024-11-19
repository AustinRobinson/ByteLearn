import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, tap, throwError } from 'rxjs';
import { LoginFormData } from '../../pages/login/login.component';
import { environment } from '../../environments/environment';
import { SignupData } from '../../app/signup/signup-data';

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

  /**
   * Make a signup request to the API.
   *
   * @returns a cold Observable with the response
   */
  public signup(signupData: SignupData): Observable<any> {
    return this.http.post(`${environment.apiBaseUrl}/register`, signupData);
  }

  /**
   * Make a login request to the API.
   *
   * @returns a cold Observable with the response
   */
  public login(formData: LoginFormData): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${environment.apiBaseUrl}/login`, formData).pipe(
      tap((response) => {
        this.accessToken = response.accessToken;
      }),
    );
  }
}
