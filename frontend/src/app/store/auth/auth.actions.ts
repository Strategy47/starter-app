import { createAction, props } from '@ngrx/store';
import { SignInInterface, SignUpInterface } from '../../shared/interfaces/auth.interface';
import { UserInterface } from '../../shared/interfaces/user.interface';

export const login = createAction(
  '[Auth] Login',
  (payload: SignInInterface) => ({ payload })
);

export const loginFromToken = createAction(
  '[Auth] Login From Token'
);

export const loginSuccess = createAction(
  '[Auth] Login Success',
  props<{ token: string, fromApi: boolean }>()
);

export const loginFailure = createAction(
  '[Auth] Login Failure',
  props<{ error: string }>()
);

export const logout = createAction(
  '[Auth] Logout'
);

export const register = createAction(
  '[Auth] Registration',
  (payload: SignUpInterface) => ({ payload })
);

export const registerSuccess = createAction(
  '[Auth] Registration Success',
  props<{ user: UserInterface }>()
);

export const registerFailure = createAction(
  '[Auth] Registration Failure',
  props<{ error: string }>()
);
