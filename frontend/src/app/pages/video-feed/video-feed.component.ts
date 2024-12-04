import { Component, OnInit } from '@angular/core';
import { VideoService } from '../../services/video/video.service';
import { HeaderComponent } from "../../components/header/header.component";
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'app-video-feed',
  standalone: true,
  imports: [HeaderComponent, ReactiveFormsModule, DatePipe],
  templateUrl: './video-feed.component.html',
  styleUrl: './video-feed.component.css'
})
export class VideoFeedComponent implements OnInit {

  public isDetailsOpen = false;
  public details = {
    "id": "12345678-abcd-1234-abcd-1234567890ab",
    "title": "Learn Python in 60 Seconds",
    "description": "Quick Python tutorial for beginners",
    "video_url": "./smile.mp4",
    "created_at": "2024-12-03T21:09:47.103Z",
    "user": {
      "id": "12345678-abcd-1234-abcd-1234567890ab",
      "username": "techteacher"
    },
    "tags": [
      {
        "id": "12345678-abcd-1234-abcd-1234567890ab",
        "tag": "python"
      },
      {
        "id": "12345678-abcd-1234-abcd-1234567890ab",
        "tag": "technology"
      }
    ],
    "is_liked": false,
    "has_watched": false,
    "comment_count": 2,
    "like_count": 56,
    "matches_interests": true
  }

  public isVideoLiked = false;

  public isCommentsOpen = false;
  public comments = [
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
    {
      username: '@JohnSmith',
      comment: 'Thanks for sharing!',
      likes: 8,
      isLiked: false,
    },
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
    {
      username: '@JaneDoe',
      comment: 'This is an amazing post! He really likes it! Also, this is a really long comment, so if you want to see the like button, it\'s going to be a long ways down! 😊',
      likes: 12,
      isLiked: false,
    },
  ];

  public commentForm = new FormGroup({
    comment: new FormControl(''),
  });

  public constructor(private videoService: VideoService) { }

  public ngOnInit(): void {

  }

  public previousVideo(): void {
    console.log('Clicked Previous');
  }

  public nextVideo(): void {
    console.log('Clicked Next');
  }

  public toggleDetails(): void {
    this.isDetailsOpen = !this.isDetailsOpen;
  }

  public toggleVideoLike(): void {
    if (!this.isVideoLiked) {
      ++this.details.like_count;
    } else {
      --this.details.like_count;
    }
    this.isVideoLiked = !this.isVideoLiked;
  }

  public toggleComments(): void {
    this.isCommentsOpen = !this.isCommentsOpen;
  }

  public clearComment(): void {
    this.commentForm.reset();
  }

  public onComment(): void {
    console.log(this.commentForm.get('comment')?.value);
  }

  public toggleLike(comment: any): void {
    if (!comment.isLiked) {
      ++comment.likes;
    } else {
      --comment.likes;
    }
    comment.isLiked = !comment.isLiked;
  }
}
