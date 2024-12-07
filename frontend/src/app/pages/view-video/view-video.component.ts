import { Component, OnChanges, OnDestroy, OnInit, signal, SimpleChanges } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { ActivatedRoute } from '@angular/router';
import { VideoPlayerComponent } from '../../components/video-player/video-player.component';
import { VideoService } from '../../services/video/video.service';
import { map, Observable, Subscription, switchMap } from 'rxjs';
import { AsyncPipe } from '@angular/common';

// Page to view a single video whose ID is a route parameter
@Component({
  selector: 'app-view-video',
  standalone: true,
  imports: [HeaderComponent, VideoPlayerComponent, AsyncPipe],
  templateUrl: './view-video.component.html',
  styleUrl: './view-video.component.css'
})
export class ViewVideoComponent implements OnInit, OnDestroy {

  // ID of the video
  public videoId = signal('');
  // S3 key of the video wrapped in an observable -> updated when HTTP request
  // finishes
  public videoS3Key$!: Observable<string>;

  // Subscription to route parameter changing
  private routeSubscription!: Subscription;

  public constructor(
    private route: ActivatedRoute,
    private videoService: VideoService,
  ) {}

  // Subscribe to route changes and nitialize video ID and S3 key
  public ngOnInit(): void {
    this.routeSubscription = this.route.paramMap.pipe(
      map(params => params.get('id') || ''),
      switchMap(videoId => {
        this.videoId.set(videoId);
        return this.videoService.getVideo(this.videoId()).pipe(
          map(details => details.s3_key)
        );
      })
    ).subscribe(s3Key => {
      this.videoS3Key$ = new Observable<string>(observer => {
        observer.next(s3Key);
        observer.complete();
      });
    });
  }

  // Unsubscribe to route subscription upon component desctruction.
  public ngOnDestroy(): void {
    if (this.routeSubscription) {
      this.routeSubscription.unsubscribe();
    }
  }
}
