import { Component } from '@angular/core';
import { HeaderComponent } from '../../components/header/header.component';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ApiErrorResponse, AuthService, LoginFormData } from '../../services/auth/auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [HeaderComponent, ReactiveFormsModule, CommonModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  loginForm = new FormGroup({
    email: new FormControl(''),
    password: new FormControl(''),
  });

  constructor(private authService: AuthService) {

  }

  onSubmit() {
    // get form values
    const formValues = this.loginForm.value;

    // authenticate using auth service
    this.authService.login(formValues as LoginFormData).subscribe({
      next: (response) => {
        console.log('Login successful:', response);
      },
      error: (error: { error: ApiErrorResponse }) => {
        console.error('Login failed:', error.error.message);

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
