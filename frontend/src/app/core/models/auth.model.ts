
export interface LoginData {
  email: string;
  password: string;
}

export interface LoginResponse {
  token?: string;
  user: {
    id: number;
    username: string;
    roles: string[];
  };
}

export interface AuthState {
  isAuthenticated: boolean;
  user: LoginResponse['user'] | null;
  loading: boolean;
}
