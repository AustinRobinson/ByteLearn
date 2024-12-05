import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Observable, of } from 'rxjs';

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
  comment_count: number;
  like_count: number;
  matches_interests: boolean;
}

export interface VideoFeed {
  id: string;
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
    // return this.http.get<VideoDetails>(`${environment.apiBaseUrl}/videos/${videoId}`);
    const video: VideoDetails = {
      id: "12345678-abcd-1234-abcd-1234567890ab",
      title: "Learn Python in 60 Seconds",
      description: "Quick Python tutorial for beginners",
      created_at: "2024-12-03T21:09:47.103Z",
      user: {
        id: "12345678-abcd-1234-abcd-1234567890ab",
        username: "techteacher"
      },
      tags: [
        {
          id: "12345678-abcd-1234-abcd-1234567890ab",
          tag: "python"
        },
        {
          id: "12345678-abcd-1234-abcd-1234567890ab",
          tag: "technology"
        }
      ],
      is_liked: false,
      has_watched: false,
      comment_count: 2,
      like_count: 56,
      matches_interests: true,
    };

    return of(video);
  }

  public getVideoUrl(videoId: string): Observable<string> {
    return of('./smile.mp4');
  }

  public videoFeed(offset: number, limit: number): Observable<VideoFeed[]> {
    // return this.http.get<VideoDetails>(`${environment.apiBaseUrl}/videos/feed?offset=${offset}&limit=${limit}`);
    const feed: VideoFeed[] = [];

    for (let i = offset * limit; i < (offset + 1) * limit; ++i) {
      feed.push({
        id: `${i}`,
      });
    }

    return of(feed);
  }
}
