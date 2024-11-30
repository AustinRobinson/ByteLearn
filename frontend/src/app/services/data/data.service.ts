import { inject, Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { GetCurrentUserResponse } from '../../models/user.model';

@Injectable({
  providedIn: 'root'
})
export class DataService {
  private apiBaseUrl = environment.apiBaseUrl;

  constructor(private http: HttpClient) { }

  public getCurrentUser(): Observable<GetCurrentUserResponse> {
    return this.http.get<GetCurrentUserResponse>(`${this.apiBaseUrl}/user`);
  }
}
