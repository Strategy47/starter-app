import { createReducer, on } from '@ngrx/store';
import * as UserActions from './user.actions';

import { appInitialState } from '../app-initial-state';
import { UserState } from './user.state';

const initialState: UserState = appInitialState.user;

export const userReducer = createReducer(
  initialState,
  on(UserActions.setUser, (state, { user }) => ({ ...state, user })),
  on(UserActions.clearUser, state => (initialState)),
  on(UserActions.updateUserSuccess, (state, { user }) => ({
    ...state,
    user,
    error: null
  })),
  on(UserActions.updateUserFailure, (state, { error }) => ({
    ...state,
    error
  }))
);
