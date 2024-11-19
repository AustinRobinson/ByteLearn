import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { SignupData } from '../../app/signup/signup-data';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

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
}
