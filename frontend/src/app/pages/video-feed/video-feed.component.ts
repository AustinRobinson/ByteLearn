import { Component, computed, OnInit, Signal, signal, untracked } from '@angular/core';
import { VideoFeed, VideoService } from '../../services/video/video.service';
import { HeaderComponent } from "../../components/header/header.component";
import { VideoPlayerComponent } from "../../components/video-player/video-player.component";

export const FEED_LIMIT: number = 20;

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
  public offset = signal(0);
  public videoIndex = signal(0);

  public currentFeed = signal<VideoFeed[]>([]);
  public videoId: Signal<string> = computed(() => {
    console.log('changed to ' + this.currentFeed()[this.videoIndex()].id);
    return this.currentFeed()[this.videoIndex()].id
  });

  public isPreviousDisabled: Signal<boolean> = computed(() => {
    return this.videoIndex() === 0 && this.offset() === 0
  });
  public isNextDisabled = signal(false);

  public constructor(
    private videoService: VideoService
  ) {}

  public ngOnInit(): void {
    this.updateFeed();
  }

  private updateFeed(): void {
    this.videoService.videoFeed(this.offset(), FEED_LIMIT).subscribe({
      next: (feed: VideoFeed[]) => {
        console.log('updated feed');
        this.currentFeed.set(feed);
      },
      error: (error: any) => {
        console.log('Error fetching feed', error);
      }
    });
  }

  public previousVideo(): void {
    console.log('Clicked Previous');
    if (this.isPreviousDisabled()) {
      return;
    }

    untracked(() => {
      this.videoIndex.update(value => value !== 0 ? value - 1 : FEED_LIMIT - 1);

      if (this.videoIndex() === FEED_LIMIT - 1) {
        this.offset.update(value => value - 1);
        this.updateFeed();
      }
    });
  }

  public nextVideo(): void {
    console.log('Clicked Next');
    if (this.isNextDisabled()) {
      return;
    }

    untracked(() => {
      this.videoIndex.update(value => (value + 1) % FEED_LIMIT);
      if (this.videoIndex() === 0) {
        this.offset.update(value => value + 1);
        this.updateFeed();
      }
    });
  }
}
