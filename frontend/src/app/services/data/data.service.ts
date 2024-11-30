import { inject, Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient, provideHttpClient, withInterceptors } from '@angular/common/http';
import { Observable } from 'rxjs';
import { authInterceptor } from '../../interceptors/auth/auth.interceptor';

@Injectable({
  providedIn: 'root'
})
export class DataService {
  private apiBaseUrl = environment.apiBaseUrl;

  constructor(private http: HttpClient) { }

  public getCurrentUser(): Observable<any> {
    return this.http.get(`${this.apiBaseUrl}/user`);
  }
}
