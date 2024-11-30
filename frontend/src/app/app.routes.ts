import { Routes } from '@angular/router';
import { LoginComponent } from './pages/login/login.component';
import { HomeComponent } from './pages/home/home.component';
import { SignupComponent } from './pages/signup/signup.component';
import { authGuard } from './guards/auth/auth.guard';
import { authResolver } from './resolvers/auth/auth.resolver';

export const routes: Routes = [
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'signup',
    component: SignupComponent,
    // resolve: {
    //   auth: authResolver,
    // },
    canActivate: [authGuard]
  },
  {
    path: '',
    component: HomeComponent,
    // resolve: {
    //   auth: authResolver,
    // },
  },
  {
    path: '**',
    redirectTo: '',
    pathMatch: 'full'
  }
];
