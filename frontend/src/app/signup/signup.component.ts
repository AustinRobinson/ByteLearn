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
  public signupForm = new FormGroup({
    firstName: new FormControl('', [Validators.required]),
    lastName: new FormControl('', [Validators.required]),
    username: new FormControl('', [Validators.required]),
    email: new FormControl('', [Validators.required, Validators.email]),
    password: new FormControl('', [Validators.required, Validators.minLength(8)]),
    confirmPassword: new FormControl('', [this.matchesField('password')]),
  })

  constructor(private router: Router, private authService: AuthService) {}

  public get firstName() {
    return this.signupForm.get('firstName');
  }

  public get lastName() {
    return this.signupForm.get('lastName');
  }

  public get username() {
    return this.signupForm.get('username');
  }

  public get email() {
    return this.signupForm.get('email');
  }

  public get password() {
    return this.signupForm.get('password');
  }

  public get confirmPassword() {
    return this.signupForm.get('confirmPassword');
  }

  public shouldShowError(control: AbstractControl | null) : boolean {
    if (control === null) {
      return false;
    }

    return control.dirty && control.errors !== null;
  }

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
    console.log(this.signupForm.value);
    console.log(this.signupForm.get('confirmPassword')!.errors);
  }

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
