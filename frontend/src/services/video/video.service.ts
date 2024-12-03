import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';

export interface uploadFormData {
  video: File;
  title: string;
  description: string;
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
}
