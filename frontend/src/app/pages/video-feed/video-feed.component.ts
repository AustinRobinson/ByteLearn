import { Component, computed, OnInit, Signal, signal } from '@angular/core';
import { FeedVideo, VideoFeedResponse, VideoService } from '../../services/video/video.service';
import { HeaderComponent } from "../../components/header/header.component";
import { VideoPlayerComponent } from "../../components/video-player/video-player.component";

export const FEED_LIMIT: number = 20;

// Page for viewing the user's video feed.
@Component({
  selector: 'app-video-feed',
  standalone: true,
  imports: [
    HeaderComponent,
    VideoPlayerComponent,
  ],
  templateUrl: './video-feed.component.html',
  styleUrl: './video-feed.component.css'
})
export class VideoFeedComponent implements OnInit {
  public total = signal(0);
  public offset = signal(0);
  public videoIndex = signal(0);

  // signal holding current video feed
  public videoFeed = signal<FeedVideo[]>([]);

  // ID of the current video based on video feed and index
  public videoId: Signal<string> = computed(() => {
    let indexInUse;
    if (this.videoIndex() === 0) {
      indexInUse = 0;
    } else {
      indexInUse = this.videoIndex() >= this.videoFeed().length
        ? this.videoIndex() - 1
        : this.videoIndex();
    }

    return this.videoFeed()[indexInUse].id;
  });

  // s3 key of the current video based on video feed and index
  public videoS3Key: Signal<string> = computed(() => {
    let indexInUse;
    if (this.videoIndex() === 0) {
      indexInUse = 0;
    } else {
      indexInUse = this.videoIndex() >= this.videoFeed().length
        ? this.videoIndex() - 1
        : this.videoIndex();
    }

    return this.videoFeed()[indexInUse].s3_key;
  });

  // whether the previous video button is disabled
  public isPreviousDisabled: Signal<boolean> = computed(() => {
    return this.videoIndex() <= 0;
  });
  // whether the next video button is disabled
  public isNextDisabled: Signal<boolean> = computed(() => {
    return this.videoIndex() === this.total() - 1;
  });

  public constructor(
    private videoService: VideoService
  ) {}

  // Get the first feed page on page load
  public ngOnInit(): void {
    this.addNextFeed();
  }

  // update the current video feed page
  private addNextFeed(): void {
    this.videoService.videoFeed(this.offset(), FEED_LIMIT).subscribe({
      next: (feed: VideoFeedResponse) => {
        this.videoFeed.update(currentFeed => {
          return [
            ...currentFeed,
            ...feed.data,
          ];
        });
        this.total.set(feed.meta.total);
      },
      error: (error: any) => {
        console.log('Error fetching feed', error);
      }
    });
  }

  // Go to previous video in feed if possible
  public previousVideo(): void {
    if (this.isPreviousDisabled()) {
      return;
    }

    this.videoIndex.update(value => value - 1);
  }

  // Go to next video in feed
  public nextVideo(): void {
    if (this.isNextDisabled()) {
      return;
    }

    this.videoIndex.update(value => value + 1);
    if (this.videoIndex() >= this.videoFeed().length) {
      this.offset.update(value => value + 1);
      this.addNextFeed();
    }
  }
}
