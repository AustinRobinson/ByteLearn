import { Routes } from '@angular/router';
import { LoginComponent } from './pages/login/login.component';
import { HomeComponent } from './pages/home/home.component';
import { SignupComponent } from './pages/signup/signup.component';
import { UploadVideoComponent } from './pages/upload-video/upload-video.component';
import { authGuard } from './guards/auth/auth.guard';
import { VideoFeedComponent } from './pages/video-feed/video-feed.component';

export const routes: Routes = [
  // {
  //   path: 'protected-route-example',
  //   component: ProtectedComponent,
  //   canActivate: [authGuard]
  // },
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'signup',
    component: SignupComponent,
  },
  {
    path: 'upload-video',
    component: UploadVideoComponent,
    canActivate: [authGuard]
  },
  {
    path: '',
    component: HomeComponent,
  },
  {
    path: 'video-feed',
    component: VideoFeedComponent,
  },
  {
    path: '**',
    redirectTo: '',
    pathMatch: 'full'
  }
];
