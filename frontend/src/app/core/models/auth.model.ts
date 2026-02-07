export interface LoginData {
  email: string;
  password: string;
}

export interface User {
  id: number;
  username: string;
  roles: string[];
}

export interface LoginResponse {
  token?: string;
  user: User;
}

export interface AuthState {
  isAuthenticated: boolean;
  user: User | null;
  loading: boolean;
  error: string | null;
}
