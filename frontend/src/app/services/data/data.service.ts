import { inject, Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { map, Observable } from 'rxjs';
import { GetCurrentUserResponse } from '../../models/user.model';
import { SearchVideosResult } from '../../models/video.model';

export interface ApiDataResponse<T> {
  message: string;
  data: T
}

@Injectable({
  providedIn: 'root'
})
export class DataService {
  private apiBaseUrl = environment.apiBaseUrl;

  constructor(private http: HttpClient) { }

  public getCurrentUser(): Observable<GetCurrentUserResponse> {
    return this.http.get<GetCurrentUserResponse>(`${this.apiBaseUrl}/user`);
  }

  // Get search results on all video fields (title, description, tags, users)
  public searchVideos(query: string): Observable<SearchVideosResult[]> {
    const headers = new HttpHeaders();
    headers.append('Accept', 'application/json')

    return this.http.get<ApiDataResponse<SearchVideosResult[]>>(`${environment.apiBaseUrl}/videos/search/all?search=${query}`, {
      headers: headers,
    }).pipe(map((response: ApiDataResponse<SearchVideosResult[]>) => response.data));
  }
}
