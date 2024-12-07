import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { map, Observable, of } from 'rxjs';
import { ApiDataResponse } from '../data/data.service';

// Interface for upload video data
export interface uploadFormData {
  video: File;
  title: string;
  description: string;
}

export interface UploadVideoResponse {
  id: string;
}

// Interface for task tags
export interface Tag {
  id: string,
  tag: string,
}

// Interface for a video's details
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

// Interface for each entry in the video feed
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
  public uploadVideo(data: uploadFormData): Observable<UploadVideoResponse> {
    const formData = new FormData();
    formData.append('video', data.video, data.video.name);
    formData.append('title', data.title);
    formData.append('description', data.description);

    const headers = new HttpHeaders();
    headers.append('Content-Type', 'multipart/form-data');
    headers.append('Accept', 'application/json')

    return this.http.post<ApiDataResponse<UploadVideoResponse>>(`${environment.apiBaseUrl}/videos/upload`, formData, {
      headers: headers,
    }).pipe(
      map(response => response.data)
    );
  }

  // Like/dislike the video with the given ID
  public toggleVideoLike(videoId: string): Observable<any> {
    const data = {
      video_id: videoId,
    };

    // return this.http.post(`${environment.apiBaseUrl}/videos/likes/toggle`, data);
    return of(data);
  }

  // Get the video with the given ID
  public getVideo(videoId: string): Observable<VideoDetails> {
    const url = `${environment.apiBaseUrl}/videos/${videoId}`

    return this.http.get<ApiDataResponse<VideoDetails>>(url).pipe(
      map(response => response.data)
    );
  }

  // Get a temporary link to the video from the back end
  public getVideoUrl(s3Key: string): Observable<string> {
    const url = `${environment.apiBaseUrl}/videos/url`;

    return this.http.post<ApiDataResponse<string>>(url, { s3_key: s3Key }).pipe(
      map(response => response.data)
    );
    // return of('./smile.mp4');
  }

  // Get the page of the video feed at the given offset with the given limit
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
