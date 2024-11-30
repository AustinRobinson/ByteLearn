import { HttpClient, HttpErrorResponse, HttpResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable, of, tap, throwError } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Router } from '@angular/router';

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
  access_token: string;
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
  private _accessToken: string | null = null;

  constructor(private http: HttpClient, private router: Router) { }


  /**
     * Get the access token.
     *
     * @returns the access token or null if it is not set
     */
  public get accessToken(): string | null {
    return this._accessToken
  }

  public isAuthenticated(): boolean {
    return !!this._accessToken;
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
        this._accessToken = response.access_token;
      }),
    );
  }

  /**
   * Make a refresh request to the API.
   *
   * @returns a cold Observable with the response
   */
  public refresh(): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${environment.apiBaseUrl}/refresh`, null, {
      withCredentials: true,
    }).pipe(
      tap((response) => {
        this._accessToken = response.access_token;
      }),
    );
  }

  /**
   * Make a logout request to the API.
   *
   * @returns a cold Observable with the response
   */
  public logout(): Observable<any> {
    return this.http.post(`${environment.apiBaseUrl}/logout`, null, {
      observe: 'response',
      withCredentials: true,
    }).pipe(
      tap((response: HttpResponse<any>) => {
        console.log('Logging out');
        if (response.status == 204) {
          this._accessToken = null;
          this.router.navigateByUrl('/');
        } else {
          throw new Error('Failed to logout');
        }
      }),
    );
  }
}
