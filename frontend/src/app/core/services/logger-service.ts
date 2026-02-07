import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { LogLevel } from '../models/logger.model';

@Injectable({
  providedIn: 'root',
})
export class LoggerService {

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
  }
}
