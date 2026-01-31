import { inject, Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { LogLevel } from '../models/logger.model';
import { HotToastService, ToastOptions } from '@ngxpert/hot-toast';

@Injectable({
  providedIn: 'root',
})
export class LoggerService {
  private toast = inject(HotToastService);

  private readonly currentLevel = environment.logLevel;

  private readonly levelWeights: Record<LogLevel, number> = {
    [LogLevel.DEBUG]: 0,
    [LogLevel.ERROR]: 1,
    [LogLevel.OFF]: 2,
  };

  private shouldLog(level: LogLevel): boolean {
    return this.levelWeights[level] >= this.levelWeights[this.currentLevel];
  }

  debug(message: string, ...args: any[]) {
    if (this.shouldLog(LogLevel.DEBUG)) {
      console.log(`%c[DEBUG] ${message}`, 'color: #7f8c8d', ...args);
    }
  }

  error(message: string, ...args: any[]) {
    if (this.shouldLog(LogLevel.ERROR)) {
      console.error(`%c[ERROR] ${message}`, 'color: #e74c3c; font-weight: bold', ...args);
    }

    this.toast.error(message, this.getOptions('error'));
  }

  private getOptions(type: 'error' | 'success' | 'info'): ToastOptions<any> {
    const palette = {
      error:   { border: '#991b1b', text: '#991b1b', bg: '#fef2f2', icon: '#dc2626' },
      success: { border: '#166534', text: '#166534', bg: '#f0fdf4', icon: '#16a34a' },
      info:    { border: '#075985', text: '#075985', bg: '#f0f9ff', icon: '#0284c7' }
    };

    const color = palette[type];

    return {
      duration: 5000,
      dismissible: true,
      style: {
        border: `1px solid ${color.border}`,
        padding: '16px',
        color: color.text,
        background: color.bg,
        fontWeight: '500'
      },
      iconTheme: {
        primary: color.icon,
        secondary: color.bg,
      },
    };
  }
}
