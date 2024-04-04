  import { Injectable } from '@angular/core';
  import { Actions, createEffect, ofType } from '@ngrx/effects';
  import { map, tap, catchError, switchMap } from 'rxjs/operators';
  import { of } from 'rxjs';
  import { AuthenticationService } from '../../core/services/authentication.service';
  import * as AuthActions from './auth.actions';
  import * as UserActions from '../user/user.actions';
  import { jwtDecode } from 'jwt-decode';
  import { StorageService } from '../../core/services/storage.service';
  import { Store } from '@ngrx/store';
  import { Router } from '@angular/router';
  import {SignInInterface, SignUpInterface} from '../../shared/interfaces/auth.interface';
  import { ToastService } from '../../core/services/toast.service';
  import { TranslateService } from '@ngx-translate/core';
  import { UserInterface } from '../../shared/interfaces/user.interface';

  @Injectable()
  export class AuthEffects {

    constructor(
      private actions$: Actions,
      private authService: AuthenticationService,
      private storageService: StorageService,
      private store: Store,
      private router: Router,
      private toastService: ToastService,
      private translate: TranslateService,
    ) {}

    login$ = createEffect(() =>
      this.actions$.pipe(
        ofType(AuthActions.login),
        switchMap(({ payload }: { payload: SignInInterface }) =>
          this.authService.logIn(payload).pipe(
            map(token => AuthActions.loginSuccess({ token: token.token, fromApi: true })),
            catchError(error => {
              const errorMsg = error.error?.detail || error.error?.message;
              return of(AuthActions.loginFailure({
                error: this.translate.instant(errorMsg || 'unknown error')
              }));
            })
          )
        )
      )
    );
    loginFromToken$ = createEffect(() => this.actions$.pipe(
      ofType(AuthActions.loginFromToken),
      tap((action) => {
        this.storageService.get('token').then((token) => {
          if (token && !this.authService.isTokenExpired(token)) {
            this.store.dispatch(AuthActions.loginSuccess({ token, fromApi: false }));

            this.storageService.get('canLoadFromToken').then((canLoadFromToken) => {
              if (false === canLoadFromToken) {
                this.store.dispatch(UserActions.loadUser()); // if user is updated don't load from token
              }
            })
          }
        })
      })
    ), { dispatch: false });

    logInSuccess$ = createEffect(() => this.actions$.pipe(
      ofType(AuthActions.loginSuccess),
      tap((action) => {
        this.storageService.set('token', action.token);
        this.toastService.presentToast("Sign in success!", 4000, 'custom-success-toast');
        if (action.fromApi) {
          this.storageService.set('canLoadFromToken', true)
        }

        let decodedToken = jwtDecode(action.token) as { [key: string]: any };
        const user: UserInterface = decodedToken['user'];
        this.store.dispatch(UserActions.setUser({ user }));
        this.authService.redirectUserByRole(user.roles);
      })
    ), { dispatch: false });

    logInFailure$ = createEffect(() => this.actions$.pipe(
      ofType(AuthActions.loginFailure),
      tap((action) => {
        this.toastService.presentToast(action.error, 4000, 'custom-warning-toast');
      })
    ), { dispatch: false });
    logout$ = createEffect(() => this.actions$.pipe(
      ofType(AuthActions.logout),
      tap((action) => {
        this.storageService.remove('token');
        this.store.dispatch(UserActions.clearUser());
        this.router.navigateByUrl('/auth', {replaceUrl: true})
      })
    ), { dispatch: false });


    register$ = createEffect(() =>
      this.actions$.pipe(
        ofType(AuthActions.register),
        switchMap(({ payload }: { payload: SignUpInterface }) =>
          this.authService.register(payload).pipe(
            map(user => AuthActions.registerSuccess({ user: user })),
            catchError(error => {
              const errorMsg = error.error?.detail || error.error?.message;
              return of(AuthActions.registerFailure({
                error: this.translate.instant(errorMsg || 'unknown error')
              }));
            })
          )
        )
      )
    );

    registerSuccess$ = createEffect(() => this.actions$.pipe(
      ofType(AuthActions.registerSuccess),
      tap((action) => {
        this.toastService.presentToast("Sign up success!", 4000, 'custom-success-toast');

         this.router.navigateByUrl('/verify', {replaceUrl: true})
      })
    ), { dispatch: false });

    registerFailure$ = createEffect(() => this.actions$.pipe(
      ofType(AuthActions.registerFailure),
      tap((action) => {
        this.toastService.presentToast(action.error, 4000, 'custom-warning-toast');
      })
    ), { dispatch: false });
  }
