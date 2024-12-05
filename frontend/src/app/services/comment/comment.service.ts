import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';

export interface Comment {
  id: string,
  username: string,
  comment: string,
  likes: number
  is_liked: boolean,
  does_user_own: boolean,
}

@Injectable({
  providedIn: 'root'
})
export class CommentService {

  constructor(private http: HttpClient) { }

  public toggleCommentLike(commentId: string): Observable<any> {
    const data = {
      comment_id: commentId,
    };

    // return this.http.post(`${environment.apiBaseUrl}/comments/likes/toggle`, data);
    return of(data);
  }

  public getVideoComments(videoId: string): Observable<Comment[]> {
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

    return of(comments);
  }
}
