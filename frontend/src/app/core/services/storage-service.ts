import { inject, Injectable } from '@angular/core';
import * as CryptoJS from 'crypto-js';
import { environment } from '../../../environments/environment';
import { LoggerService } from './logger-service';

@Injectable({ providedIn: 'root' })
export class StorageService {
  private readonly SECRET_KEY = environment.cryptoKey;
  private readonly ENCRYPT = environment.encryptStorage;

  private readonly logger = inject(LoggerService);

  set(key: string, value: any): void {
    if (!value) return;

    try {
      const data = JSON.stringify(value);

      const content = this.ENCRYPT ? CryptoJS.AES.encrypt(data, this.SECRET_KEY).toString() : data;

      localStorage.setItem(key, content);
    } catch (e) {
      this.logger.error('Error guardando datos en storage', e);
    }
  }

  get<T>(key: string): T | null {
    const data = localStorage.getItem(key);
    if (!data) return null;

    try {
      let decryptedData: string;
      if (this.ENCRYPT) {
        const bytes = CryptoJS.AES.decrypt(data, this.SECRET_KEY);
        decryptedData = bytes.toString(CryptoJS.enc.Utf8);
      } else {
        decryptedData = data;
      }

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
