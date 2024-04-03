import {createFeatureSelector, createSelector} from '@ngrx/store';
import { AuthState } from './auth.state';

export const selectAuthState = createFeatureSelector<AuthState>('auth');

export const selectToken = createSelector(
  selectAuthState,
  (state) => state.token
);

export const selectError = createSelector(
  selectAuthState,
  (state) => state.error
);

export const selectIsLoading = createSelector(
  selectAuthState,
  (state) => state.isLoading
);

export const selectIsAuthenticated = createSelector(
  selectAuthState,
  (state) => state.isAuthenticated
);

