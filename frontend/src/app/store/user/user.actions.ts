import { createAction, props } from '@ngrx/store';
import { UserInterface } from '../../shared/interfaces/user.interface';

export const setUser = createAction('[User] Set User', props<{ user: UserInterface }>());
export const loadUser = createAction('[User] Load User');
export const clearUser = createAction('[User] Clear User');
export const userError = createAction('[User] User Error', props<{ error: any }>());

export const updateUserSuccess = createAction(
  '[User] Update User Success',
  props<{ user: UserInterface }>()
);

export const updateUserFailure = createAction(
  '[User] Update User Failure',
  props<{ error: any }>()
);
