export interface LoginData {
  email: string;
  password: string;
  rememberMe: boolean;
}

export interface User {
  id: number;
  username: string;
  roles: string[];
}

export interface LoginResponse {
  accessToken?: string;
  user: User;
}

export interface AuthState {
  isAuthenticated: boolean;
  user: User | null;
  loading: boolean;
  error: string | null;
}
