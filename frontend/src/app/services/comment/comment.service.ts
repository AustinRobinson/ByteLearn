import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { environment } from '../../../environments/environment';

// Interface for a single comment
export interface Comment {
  id: string;
  username: string;
  comment: string;
  likes: number;
  is_liked: boolean;
  does_user_own: boolean;
}

// Interface for a list of comments, including comment count
export interface Comments {
  comment_count: number;
  comments: Comment[];
}

// Service for managing video comments
@Injectable({
  providedIn: 'root'
})
export class CommentService {

  constructor(private http: HttpClient) { }

  // get the given video's comments
  public getVideoComments(videoId: string): Observable<Comments> {
    // return this.http.get<Comment[]>(`/comments/video/${videoId}`);
    const comments: Comment[] = [
      {
        id: '1',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '2',
        username: '@JohnSmith',
        comment: 'Thanks for sharing!',
        likes: 8,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '3',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '4',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '5',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '6',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '7',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '8',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
      {
        id: '9',
        username: '@JaneDoe',
        comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
        likes: 12,
        is_liked: false,
        does_user_own: false,
      },
    ];

    const commentsResult: Comments = {
      comment_count: 9,
      comments: comments,
    };

    return of(commentsResult);
  }

  // like/dislike a comment
  public toggleCommentLike(commentId: string): Observable<any> {
    const data = {
      comment_id: commentId,
    };

    // return this.http.post(`${environment.apiBaseUrl}/comments/likes/toggle`, data);
    return of(data);
  }

  public createComment(videoId: string): Observable<any> {
    const data = {
      video_id: videoId,
    };
    const url = `${environment.apiBaseUrl}/comments/likes/toggle`;

    // return this.http.post(url, data);
    return of(data);
  }
}
