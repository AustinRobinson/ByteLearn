import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { map, Observable, of } from 'rxjs';

interface ApiDataResponse<T> {
  message: string;
  data: T
}

export interface uploadFormData {
  video: File;
  title: string;
  description: string;
}

export interface Tag {
  id: string,
  tag: string,
}

export interface VideoDetails {
  id: string;
  s3_key: string;
  title: string;
  description: string;
  created_at: string;
  user: {
    id: string,
    username: string,
  };
  tags: Tag[];
  has_watched: boolean;
  is_liked: boolean;
  like_count: number;
}

export interface VideoFeed {
  id: string;
  s3_key: string;
}

@Injectable({
  providedIn: 'root'
})
export class VideoService {

  // construct the video service with the injected HTTP client
  constructor(private http: HttpClient) { }

  // Make a request to the back-end API to upload the video. Puts the video
  // data (video file, title, description) in a FormData object and sends the
  // data as multipart form data.
  public uploadVideo(data: uploadFormData) {
    const formData = new FormData();
    formData.append('video', data.video, data.video.name);
    formData.append('title', data.title);
    formData.append('description', data.description);

    const headers = new HttpHeaders();
    headers.append('Content-Type', 'multipart/form-data');
    headers.append('Accept', 'application/json')

    return this.http.post(`${environment.apiBaseUrl}/videos/upload`, formData, {
      headers: headers,
    });
  }

  public toggleVideoLike(videoId: string): Observable<any> {
    const data = {
      video_id: videoId,
    };

    // return this.http.post(`${environment.apiBaseUrl}/videos/likes/toggle`, data);
    return of(data);
  }

  public getVideo(videoId: string): Observable<VideoDetails> {
    const url = `${environment.apiBaseUrl}/videos/${videoId}`

    return this.http.get<ApiDataResponse<VideoDetails>>(url).pipe(
      map(response => response.data)
    );
  }

  public getVideoUrl(s3Key: string): Observable<string> {
    const url = `${environment.apiBaseUrl}/videos/url`;

    return this.http.post<ApiDataResponse<string>>(url, { s3_key: s3Key }).pipe(
      map(response => response.data)
    );
    // return of('./smile.mp4');
  }

  public videoFeed(offset: number, limit: number): Observable<VideoFeed[]> {
    // return this.http.get<VideoDetails>(`${environment.apiBaseUrl}/videos/feed?offset=${offset}&limit=${limit}`);
    const feed: VideoFeed[] = [];

    for (let i = offset * limit; i < (offset + 1) * limit; ++i) {
      feed.push({
        id: `${i}`,
        s3_key: `${i}`,
      });
    }

    return of(feed);
  }
}
