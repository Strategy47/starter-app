import { UserState } from './user/user.state';
import { AuthState } from './auth/auth.state';

export type AppState = {
  auth: AuthState;
  user: UserState;
};
