import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { DataService } from '../../services/data/data.service';
import { SearchVideosResult } from '../../models/video.model';
import { Router } from '@angular/router';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { debounceTime, distinctUntilChanged, Subject, switchMap } from 'rxjs';

@Component({
  selector: 'app-search',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './search.component.html',
  styleUrl: './search.component.css'
})
export class SearchComponent {
  private searchSubject = new Subject<string>();
  searchQuery: string = '';
  videos: Array<SearchVideosResult> = [];

  constructor(private dataService: DataService, private router: Router) {
    dayjs.extend(relativeTime);
  }

  /**
   * Setup a debounced search observable to automatically search for videos
   * after some time has passed since the last input event
   */
  ngOnInit(): void {
    // create debounced search observable
    this.searchSubject.pipe(
      debounceTime(300), // Wait 300ms after the last input event
      distinctUntilChanged(), // Only emit when the current value is different than the last
      switchMap((query: string) => {
        if (this.searchQuery.trim()) {
          // search all video attributes using the data service
          return this.dataService.searchVideos(query);
        }
        return [];
      })
    ).subscribe({
      next: (value: Array<SearchVideosResult>) => {
        this.videos = value;
      },
      error: (error: any) => {
        console.error(error);
      }
    });
  }

  /**
   * Updates the search query when the user types in the search input
   * @param query Handle search input
   */
  onSearchInput(): void {
    this.searchSubject.next(this.searchQuery);
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
