import { Component, OnInit, signal } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { ActivatedRoute } from '@angular/router';
import { VideoPlayerComponent } from '../../components/video-player/video-player.component';
import { VideoDetails, VideoService } from '../../services/video/video.service';
import { map, Observable } from 'rxjs';
import { AsyncPipe } from '@angular/common';

@Component({
  selector: 'app-view-video',
  standalone: true,
  imports: [HeaderComponent, VideoPlayerComponent, AsyncPipe],
  templateUrl: './view-video.component.html',
  styleUrl: './view-video.component.css'
})
export class ViewVideoComponent implements OnInit {

  public videoId = signal('');
  public videoS3Key$!: Observable<string>;

  public constructor(
    private route: ActivatedRoute,
    private videoService: VideoService,
  ) {}

  public ngOnInit(): void {
    const videoId = this.route.snapshot.paramMap.get('id') || '';
    if (!videoId) {
      return;
    }

    this.videoId.set(videoId);
    this.videoS3Key$ = this.videoService.getVideo(this.videoId()).pipe(
      map(details => details.s3_key)
    );
  }
}
