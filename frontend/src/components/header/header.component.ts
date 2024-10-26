import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

interface HeaderItem {
  label: string;
  link: string;
}

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './header.component.html',
  styleUrl: './header.component.css'
})
export class HeaderComponent {
  isMenuOpen = false;

  headerItems: HeaderItem[] = [];

  toggleMenu() {
    this.isMenuOpen = !this.isMenuOpen;
  }
}
