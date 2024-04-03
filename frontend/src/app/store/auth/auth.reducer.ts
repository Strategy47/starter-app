import { createReducer, on } from '@ngrx/store';
import * as AuthActions from './auth.actions';
import { appInitialState } from '../app-initial-state';
import { AuthState } from './auth.state';

const initialState: AuthState = appInitialState.auth;

export const authReducer = createReducer(initialState,
  on(AuthActions.login, state => ({
    ...state,
    isLoading: true })),
  on(AuthActions.loginSuccess, (state, { token }) => ({
    ...state,
    token,
    isLoading: false,
    isAuthenticated: true,
    error: null
  })),
  on(AuthActions.loginFailure, (state, { error }) => ({
    ...state,
    error,
    isLoading: false
  })),
  on(AuthActions.register, state => ({
    ...state,
    isLoading: true })),
  on(AuthActions.registerSuccess, (state, { user }) => ({
    ...state,
    user,
    isLoading: false,
    error: null
  })),
  on(AuthActions.registerFailure, (state, { error }) => ({
    ...state,
    error,
    isLoading: false
  })),
  on(AuthActions.logout, () => (initialState))
);
