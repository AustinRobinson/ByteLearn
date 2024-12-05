import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { DataService } from '../../services/data/data.service';
import { SearchVideosResult } from '../../models/video.model';
import { Router } from '@angular/router';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

@Component({
  selector: 'app-search',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './search.component.html',
  styleUrl: './search.component.css'
})
export class SearchComponent {
  searchQuery: string = '';
  videos: Array<SearchVideosResult> = [];

  constructor(private dataService: DataService, private router: Router) {
    dayjs.extend(relativeTime);
  }

  /**
   * Search for videos based on the search query
   */
  onSearch() {
    if (this.searchQuery.trim()) {

      // search all video attributes using the data service
      this.dataService.searchVideos(this.searchQuery).subscribe({
        next: (value) => {
          this.videos = value;
        },
        error: (error: any) => {
          console.log(error);
        }
      });
    } else {
      this.videos = [];
    }
  }

  /**
   * Handle the selection of a video from the search results
   * @param video The video that was selected
   */
  selectVideo(video: SearchVideosResult) {
    // clear the search query and navigate to the video page
    this.searchQuery = '';
    this.router.navigate(['/video', video.id])
  }

  /**
   * Convert a date string to a relative time string
   * @param date ISO8601 date string
   * @returns Relative time string
   */
  dateToRelative(date: string): string {
    return dayjs(date).fromNow();
  }
}
