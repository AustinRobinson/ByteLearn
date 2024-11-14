import { Component } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { AbstractControl, FormControl, FormGroup, ReactiveFormsModule, ValidationErrors, ValidatorFn, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth/auth.service';
import { signupData } from './signup-data';
import { Router } from '@angular/router';

@Component({
  selector: 'app-signup',
  standalone: true,
  imports: [
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
    confirmPassword: new FormControl('', [this.matchesField('password')]),
  })

  constructor(private router: Router, private authService: AuthService) {}

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
  public shouldShowError(control: AbstractControl | null) : boolean {
    if (control === null) {
      return false;
    }

    return control.dirty && control.errors !== null;
  }

  /**
   * Called when the form is submitted. Checks that the fields are valid and
   * makes a request to the API to create a new account.
   */
  public onSubmit() : void {
    if (this.signupForm.invalid) {
      console.log('Errors in form');
      return;
    }

    this.authService.signup(this.signupForm.value as signupData).subscribe({
      next: (value) => {
        this.router.navigateByUrl('/video-feed');
      },
      error: (err) => {
        console.error('Error signing up', err);
      }
    });
  }

  /**
   * Custom validator function that checks if the value in the form control
   * matches the value in the form control with the given name.
   *
   * @returns an object if the values do not match, null otherwise
   */
  private matchesField(formControlName: string) : ValidatorFn {
    return (control: AbstractControl) : ValidationErrors | null => {
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
