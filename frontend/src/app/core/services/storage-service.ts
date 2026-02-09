import { inject, Injectable } from '@angular/core';
import * as CryptoJS from 'crypto-js';
import { environment } from '../../../environments/environment';
import { LoggerService } from './logger-service';

@Injectable({ providedIn: 'root' })
export class StorageService {
  private readonly SECRET_KEY = environment.cryptoKey;

  private logger = inject(LoggerService);

  set(key: string, value: any): void {
    if (!value) return;

    try {
      const data = JSON.stringify(value);
      const encrypted = CryptoJS.AES.encrypt(data, this.SECRET_KEY).toString();
      localStorage.setItem(key, encrypted);
    } catch (e) {
      this.logger.error('Error guardando datos en storage', e);
    }
  }

  get<T>(key: string): T | null {
    const encrypted = localStorage.getItem(key);
    if (!encrypted) return null;

    try {
      const bytes = CryptoJS.AES.decrypt(encrypted, this.SECRET_KEY);
      const decryptedData = bytes.toString(CryptoJS.enc.Utf8);

      if (!decryptedData) return null;

      return JSON.parse(decryptedData) as T;
    } catch (e) {
      this.remove(key);
      return null;
    }
  }

  remove(key: string): void {
    localStorage.removeItem(key);
  }

  clear(): void {
    localStorage.clear();
  }
}
