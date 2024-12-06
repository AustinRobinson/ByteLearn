import { Component, effect, input, OnInit, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { VideoDetails, VideoService } from '../../services/video/video.service';
import { AsyncPipe, DatePipe } from '@angular/common';
import { Observable } from 'rxjs';
import { Comments, CommentService } from '../../services/comment/comment.service';

// Component to play a video and display its details, including comments
@Component({
  selector: 'app-video-player',
  standalone: true,
  imports: [ReactiveFormsModule, AsyncPipe, DatePipe],
  templateUrl: './video-player.component.html',
  styleUrl: './video-player.component.css'
})
export class VideoPlayerComponent {
  // video ID input from parent component
  public videoId = input('');
  // video S3 key input from parent component
  public videoS3Key = input('');

  // video URL from back-end request
  public videoUrl$!: Observable<string>;
  // video details from back-end request
  public videoDetails$!: Observable<VideoDetails>;
  // video comments from back-end request
  public videoComments$!: Observable<Comments>;

  // whether the video details or comments accordions are opened or not
  public isDetailsOpen = signal(false);
  public isCommentsOpen = signal(false);

  // Form for creating a comment
  public commentForm = new FormGroup({
    comment: new FormControl('', Validators.required),
  });

  public constructor(
    private videoService: VideoService,
    private commentService: CommentService,
  ) {
    // whenever video ID changes, update the video's details and comments
    effect(() => {
      if (this.videoId()) {
        this.videoDetails$ = this.videoService.getVideo(this.videoId());
        this.videoComments$ = this.commentService.getVideoComments(this.videoId());
      }
    });

    // whenever the video S3 key changes, update the video's URL
    effect(() => {
      if (this.videoS3Key()) {
        this.videoUrl$ = this.videoService.getVideoUrl(this.videoS3Key());
      }
    });
  }

  // Get the comment form control from the form
  public get commentControl() {
    return this.commentForm.get('comment')!;
  }

  // Toggle opening/closing the video details accordion
  public toggleDetails(): void {
    this.isDetailsOpen.update(value => !value);
  }

  // Toggle likeing/disliking the video
  public toggleVideoLike(): void {
    this.videoService.toggleVideoLike(this.videoId()).subscribe({
      next: () => {
        this.videoDetails$ = this.videoService.getVideo(this.videoId());
      },
      error: (err) => {
        console.log('Error toggling video like', err);
      }
    });
  }

  // Toggle opening/closing the video comments accordion
  public toggleComments(): void {
    this.isCommentsOpen.update(value => !value);
  }

  // Create a new comment and update the video's comments upon successful
  // creation
  public onComment(): void {
    if (this.commentForm.invalid) {
      return;
    }

    this.commentService.createComment(this.videoId()).subscribe({
      next: (value) => {
        this.videoComments$ = this.commentService.getVideoComments(this.videoId());
      },
      error: (err) => {
        console.log('Error creating comment', err);

      }
    });
  }

  // Toggle liking/disliking a particular comment -> updates the video's comments
  // upon successful operation
  public toggleCommentLike(commentId: string): void {
    this.commentService.toggleCommentLike(commentId).subscribe({
      next: () => {
        this.videoComments$ = this.commentService.getVideoComments(this.videoId());
      },
      error: (err) => {
        console.log('Error toggling comment like', err);
      }
    });
  }
}
