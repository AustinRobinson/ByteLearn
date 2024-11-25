import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, tap, throwError } from 'rxjs';
import { environment } from '../../environments/environment';

/**
 * Signup form data interface.
 */
export interface SignupFormData {
  first_name: string;
  last_name: string;
  username: string;
  email: string;
  password: string;
  password_confirmation: string;
};

/**
 * Login form data interface.
 */
export interface LoginFormData {
  email: string;
  password: string;
};

/**
 * Login response interface.
 */
export interface LoginResponse {
  accessToken: string;
}


/**
 * API error response interface.
 */
export interface ApiErrorResponse extends HttpErrorResponse {
  error: {
    // general message about the error
    message: string;
    errors: {
      // key-value pairs where the key is the field name and the value is an array of error messages
      [key: string]: string[];
    };
  }
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
  public signup(signupData: SignupFormData): Observable<any> {
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
