import { Routes } from '@angular/router';
import { LoginComponent } from './pages/login/login.component';
import { HomeComponent } from './pages/home/home.component';
import { SignupComponent } from './pages/signup/signup.component';
import { UploadVideoComponent } from './pages/upload-video/upload-video.component';
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
    path: '',
    component: HomeComponent,
  },
  {
    path: 'upload-video',
    component: UploadVideoComponent,
  },
  {
    path: 'video-feed/:videoId',
    component: VideoFeedComponent,
  },
  {
    path: '**',
    redirectTo: '',
    pathMatch: 'full'
  }
];
