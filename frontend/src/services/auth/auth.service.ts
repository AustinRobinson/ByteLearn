import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { LoginFormData } from '../../pages/login/login.component';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  constructor(private http: HttpClient) {

  }

  login(formData: LoginFormData) {
    return this.http.post(`${environment.apiBaseUrl}/login`, formData);
  }
}
