import { User } from '../models/user.model';

export const MOCK_USERS: User[] = [
  {
    id: 1,
    username: 'John',
    role: 'ROLE_OPERATOR',
    lastLogin: new Date()
  }
];
