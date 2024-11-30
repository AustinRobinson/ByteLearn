import { Routes } from '@angular/router';
import { LoginComponent } from './pages/login/login.component';
import { HomeComponent } from './pages/home/home.component';
import { SignupComponent } from './pages/signup/signup.component';

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
    path: '**',
    redirectTo: '',
    pathMatch: 'full'
  }
];
