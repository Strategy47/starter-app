import { UserInterface } from '../../shared/interfaces/user.interface';

export type AuthState = {
  user: UserInterface  | null,
  token: string | null;
  error: string | null;
  isLoading: boolean;
  isAuthenticated: boolean;
}
