import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { email, form, FormField, required } from '@angular/forms/signals';
import { LoggerService } from '../../core/services/logger-service';
import { AuthService } from '../../core/services/auth-service';
import { LoginData } from '../../core/models/auth.model';
import { Router, RouterLink } from '@angular/router';

// Componentes de PrimeNG
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { ButtonModule } from 'primeng/button';
import { MessageModule } from 'primeng/message';
import { CheckboxModule } from 'primeng/checkbox';

@Component({
  selector: 'app-login',
  templateUrl: 'login.component.html',
  styleUrl: 'login.component.scss',
  imports: [
    RouterLink,
    FormField,
    InputTextModule,
    PasswordModule,
    ButtonModule,
    MessageModule,
    CheckboxModule
  ],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class LoginComponent {
  private readonly logger = inject(LoggerService);
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  protected readonly loading = this.authService.loading;
  protected readonly authError = this.authService.authError;

  loginModel = signal<LoginData>({
    email: 'john@gmail.com',
    password: 'pass',
    rememberMe: false,
  });

  loginForm = form(this.loginModel, (schemaPath) => {
    required(schemaPath.email, { message: 'Email is required' });
    email(schemaPath.email, { message: 'Enter a valid email address' });
    required(schemaPath.password, { message: 'Password is required' });
  });

  onSubmit(event: Event) {
    event.preventDefault();

    if (this.loginForm().invalid()) {
      this.logger.debug('Formulario inválido, abortando login');
      return;
    }

    const credentials = this.loginModel();

    this.authService.login(credentials).subscribe({
      next: () => this.router.navigate(['/inventory']),
      error: () => {},
    });
  }
}
