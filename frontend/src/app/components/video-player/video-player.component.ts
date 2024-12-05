import { Component, computed, input, OnInit, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { VideoDetails, VideoService } from '../../services/video/video.service';
import { AsyncPipe, DatePipe } from '@angular/common';
import { Observable } from 'rxjs';
import { Comment, CommentService } from '../../services/comment/comment.service';

@Component({
  selector: 'app-video-player',
  standalone: true,
  imports: [ReactiveFormsModule, AsyncPipe, DatePipe],
  templateUrl: './video-player.component.html',
  styleUrl: './video-player.component.css'
})
export class VideoPlayerComponent implements OnInit {
  public videoId = input('');

  public videoUrl$!: Observable<string>;
  public videoDetails$!: Observable<VideoDetails>;
  public videoComments$!: Observable<Comment[]>;

  public isDetailsOpen = signal(false);
  public isCommentsOpen = signal(false);

  public commentForm = new FormGroup({
    comment: new FormControl(''),
  });

  public constructor(
    private videoService: VideoService,
    private commentService: CommentService,
  ) {}

  public ngOnInit(): void {
    this.videoUrl$ = this.videoService.getVideoUrl(this.videoId());
    this.videoDetails$ = this.videoService.getVideo(this.videoId());
    this.videoComments$ = this.commentService.getVideoComments(this.videoId());
  }

  public toggleDetails(): void {
    this.isDetailsOpen.update(value => !value);
  }

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

  public toggleComments(): void {
    this.isCommentsOpen.update(value => !value);
  }

  public onComment(): void {
    console.log(this.commentForm.get('comment')?.value);
  }

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
