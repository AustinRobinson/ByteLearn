import { Component } from '@angular/core';
import { Router, RouterOutlet } from '@angular/router';
import { ApiErrorResponse, AuthService } from './services/auth/auth.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'frontend';

  constructor(private authService: AuthService, private router: Router) { }
}
