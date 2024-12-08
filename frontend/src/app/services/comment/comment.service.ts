import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { environment } from '../../../environments/environment';

// Interface for a single comment
export interface Comment {
  id: string;
  user: {
    id: string,
    username: string,
  };
  text: string;
  likes: number;
  is_liked: boolean;
  is_user_creator: boolean;
  created_at: string;
}

// Interface for meta information about the comments list
export interface CommentMeta {
  total_comments: boolean;
};

// Interface for a list of comments, including meta information
export interface Comments {
  data: Comment[];
  meta: CommentMeta;
}

// Service for managing video comments
@Injectable({
  providedIn: 'root'
})
export class CommentService {

  constructor(private http: HttpClient) { }

  // get the given video's comments
  public getVideoComments(videoId: string): Observable<Comments> {
    return this.http.get<Comments>(`${environment.apiBaseUrl}/video-comments/${videoId}`);
  }

  // like/dislike a comment
  public toggleCommentLike(commentId: string): Observable<any> {
    return this.http.post(`${environment.apiBaseUrl}/comments/${commentId}/like`, {});
  }

  // create a comment for the given video with the given content
  public createComment(videoId: string, comment: string): Observable<any> {
    const data = {
      comment: comment,
    };
    const url = `${environment.apiBaseUrl}/video-comments/${videoId}`;

    return this.http.post(url, data);
  }
}
