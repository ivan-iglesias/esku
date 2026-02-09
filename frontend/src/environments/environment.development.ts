import { LogLevel } from "../app/core/models/logger.model";

export const environment = {
  production: false,
  encryptStorage: false,
  cryptoKey: 'DEV_KEY_132456',
  logLevel: LogLevel.DEBUG,
  apiUrl: '/api'
};
