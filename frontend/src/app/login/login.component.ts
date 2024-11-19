import { Component } from '@angular/core';
import { HeaderComponent } from '../components/header/header.component';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ApiErrorResponse, AuthService, LoginFormData } from '../../services/auth/auth.service';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [HeaderComponent, ReactiveFormsModule, CommonModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {

  // all validation is handled server-side
  loginForm = new FormGroup({
    email: new FormControl(''),
    password: new FormControl(''),
  });

  constructor(private router: Router, private authService: AuthService) { }

  /**
   * Get the email form control.
   */
  public get email() {
    return this.loginForm.get('email');
  }

  /**
   * Get the password form control.
   */
  public get password() {
    return this.loginForm.get('password');
  }

  onSubmit() {
    // get form values
    const formValues = this.loginForm.value;

    // authenticate using auth service
    this.authService.login(formValues as LoginFormData).subscribe({
      next: (response) => {
        this.router.navigateByUrl('/video-feed');
      },
      error: (error: ApiErrorResponse) => {
        console.error('Login failed:', error.error.message);

        // check if email or password is incorrect
        if (error?.status === 401) {
          this.loginForm.setErrors({ serverError: 'Email or password is incorrect' });
          return;
        }

        const fieldErrors = error?.error?.errors;

        // check if the response has field errors
        if (fieldErrors) {
          // for each field with an error
          Object.keys(fieldErrors).forEach((field) => {
            const control = this.loginForm.get(field);
            if (control) {
              // display the first error message for the field
              control.setErrors({ serverError: fieldErrors[field][0] });
            }
          });
        }
      }
    });
  }

}
