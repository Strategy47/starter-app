import { AppState } from './app.state';

export const appInitialState: AppState = {
    auth: {
      user: null,
      token: null,
      error: null,
      isLoading: false,
      isAuthenticated: false
    },
    user: {
        user: null
    }
};
