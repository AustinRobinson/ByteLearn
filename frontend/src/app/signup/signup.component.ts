import { Component } from '@angular/core';
import { HeaderComponent } from "../components/header/header.component";
import { AbstractControl, FormControl, FormGroup, ReactiveFormsModule, ValidationErrors, ValidatorFn, Validators } from '@angular/forms';
import { ApiErrorResponse, AuthService, SignupFormData } from '../../services/auth/auth.service';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-signup',
  standalone: true,
  imports: [
    CommonModule,
    HeaderComponent,
    ReactiveFormsModule,
  ],
  templateUrl: './signup.component.html',
  styleUrl: './signup.component.css'
})
export class SignupComponent {
  // Signup form group to hold form data
  public signupForm = new FormGroup({
    firstName: new FormControl('', [Validators.required]),
    lastName: new FormControl('', [Validators.required]),
    username: new FormControl('', [Validators.required]),
    email: new FormControl('', [Validators.required, Validators.email]),
    password: new FormControl('', [Validators.required, Validators.minLength(8)]),
    confirmPassword: new FormControl('', [Validators.required, this.matchesField('password')]),
  })

  constructor(private router: Router, private authService: AuthService) { }

  /**
   * Get the firstName form control.
   */
  public get firstName() {
    return this.signupForm.get('firstName');
  }

  /**
   * Get the lastName form control.
   */
  public get lastName() {
    return this.signupForm.get('lastName');
  }

  /**
   * Get the username form control.
   */
  public get username() {
    return this.signupForm.get('username');
  }

  /**
   * Get the email form control.
   */
  public get email() {
    return this.signupForm.get('email');
  }

  /**
   * Get the password form control.
   */
  public get password() {
    return this.signupForm.get('password');
  }

  /**
   * Get the confirmPassword form control.
   */
  public get confirmPassword() {
    return this.signupForm.get('confirmPassword');
  }

  /**
   * Return a boolean indicating whether the given form control's error message
   * should be shown.
   *
   * @returns a boolean
   */
  public shouldShowError(control: AbstractControl | null): boolean {
    if (control === null) {
      return false;
    }

    return control.touched && control.errors !== null;
  }

  /**
   * Called when the form is submitted. Checks that the fields are valid and
   * makes a request to the API to create a new account.
   */
  public onSubmit(): void {
    if (this.signupForm.invalid) {
      this.signupForm.markAllAsTouched();
      return;
    }

    const signupData: SignupFormData = {
      first_name: this.firstName?.value ?? '',
      last_name: this.lastName?.value ?? '',
      username: this.username?.value ?? '',
      email: this.email?.value ?? '',
      password: this.password?.value ?? '',
      password_confirmation: this.confirmPassword?.value ?? ''
    };

    console.log(signupData);

    this.authService.signup(signupData).subscribe({
      next: (value) => {
        this.router.navigateByUrl('/video-feed');
      },
      error: (error: ApiErrorResponse) => {
        console.error('Signup failed:', error.error.message);

        const fieldErrors = error?.error?.errors;

        // set the error for each field with an error
        if (fieldErrors?.['first_name']?.length > 0) {
          this.firstName?.setErrors({ serverError: fieldErrors?.['first_name']?.[0] });
        }

        if (fieldErrors?.['last_name']?.length > 0) {
          this.lastName?.setErrors({ serverError: fieldErrors?.['last_name']?.[0] });
        }

        if (fieldErrors?.['username']?.length > 0) {
          this.username?.setErrors({ serverError: fieldErrors?.['username']?.[0] });
        }

        if (fieldErrors?.['email']?.length > 0) {
          this.email?.setErrors({ serverError: fieldErrors?.['email']?.[0] });
        }

        if (fieldErrors?.['password']?.length > 0) {
          this.password?.setErrors({ serverError: fieldErrors?.['password']?.[0] });
        }

        if (fieldErrors?.['password_confirmation']?.length > 0) {
          this.confirmPassword?.setErrors({ serverError: fieldErrors?.['password_confirmation']?.[0] });
        }
      }
    });
  }

  /**
   * Custom validator function that checks if the value in the form control
   * matches the value in the form control with the given name.
   *
   * @returns an object if the values do not match, null otherwise
   */
  private matchesField(formControlName: string): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const error = { matchesField: true };
      if (!this.signupForm) {
        return error;
      }

      const controlToMatch = this.signupForm.get(formControlName)!;
      const doesNotMatch: boolean = control.value !== controlToMatch.value;

      return doesNotMatch ? error : null;
    }
  }
}
