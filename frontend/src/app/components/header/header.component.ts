import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth/auth.service';
import { SearchComponent } from '../search/search.component';
import { Router, RouterLink } from '@angular/router';

interface HeaderItem {
  label: string;
  link: string;
}

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterLink, SearchComponent],
  templateUrl: './header.component.html',
  styleUrl: './header.component.css'
})
export class HeaderComponent {
  isMobileMenuOpen = false;
  isProfileMenuOpen = false;
  headerItems: HeaderItem[] = [];
  userProfileImage = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80';

  constructor(public authService: AuthService) {
    if (authService.isAuthenticated()) {
      this.headerItems.push({
        label: "Video Feed",
        link: "/video-feed",
      });
    }
  }

  toggleMenu() {
    this.isMobileMenuOpen = !this.isMobileMenuOpen;
  }

  toggleProfileMenu() {
    this.isProfileMenuOpen = !this.isProfileMenuOpen;
  }

  logout() {
    this.authService.logout().subscribe();
  }
}
