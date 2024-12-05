import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-search',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './search.component.html',
  styleUrl: './search.component.css'
})
export class SearchComponent {
  searchQuery: string = '';
  users: Array<{ name: string }> = [];
  videos: Array<{ title: string }> = [];

  // Simulated search results
  allUsers = [{ name: 'John Doe' }, { name: 'Jane Smith' }];
  allVideos = [{ title: 'Introduction to Angular' }, { title: 'Tailwind CSS Basics' }];

  onSearch() {
    if (this.searchQuery.trim()) {
      this.users = this.allUsers.filter((user) =>
        user.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );

      this.videos = this.allVideos.filter((video) =>
        video.title.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    } else {
      this.users = [];
      this.videos = [];
    }
  }

  selectItem(item: any) {
    console.log('Selected:', item);
    // Handle selection logic
  }
}
