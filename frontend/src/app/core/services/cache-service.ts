import { Injectable } from '@angular/core';
import { HttpResponse } from '@angular/common/http';
import { CacheEntry } from '../models/cache.model';

@Injectable({ providedIn: 'root' })
export class CacheService {
  private cache = new Map<string, CacheEntry>();
  private readonly DEFAULT_TTL = 300000; // 5 minutos por defecto

  /** Guarda una respuesta en el caché */
  put(url: string, response: HttpResponse<any>, ttl: number = this.DEFAULT_TTL): void {
    const entry: CacheEntry = {
      url,
      data: response,
      expiry: Date.now() + ttl,
    };
    this.cache.set(url, entry);
  }

  /** Recupera una respuesta si no ha expirado */
  get(url: string): HttpResponse<any> | null {
    const entry = this.cache.get(url);

    if (!entry) return null;

    if (Date.now() > entry.expiry) {
      this.cache.delete(url); // Limpieza si expiró
      return null;
    }

    return entry.data;
  }

  /** Limpia todo el caché */
  clear(): void {
    this.cache.clear();
  }
}
