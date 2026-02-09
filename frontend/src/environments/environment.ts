import { LogLevel } from "../app/core/models/logger.model";

export const environment = {
  production: true,
  cryptoKey: 'PROD_v1_SUPER_SECURE_KEY_X9Z',
  logLevel: LogLevel.ERROR,
  apiUrl: 'https://api.esku-logistics.com'
};
