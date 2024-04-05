import { Injectable } from '@angular/core';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import * as UserActions from './user.actions';
import { UserService } from '../../core/services/user.service';
import { catchError, map, switchMap, tap } from 'rxjs/operators';
import { of } from 'rxjs';
import { StorageService } from '../../core/services/storage.service';
import { TranslateService } from '@ngx-translate/core';
import { ToastService } from '../../core/services/toast.service';

@Injectable()
export class UserEffects {

  constructor(
    private actions$: Actions,
    private userService: UserService,
    private storageService: StorageService,
    private translate: TranslateService
  ) {}

  loadUser$ = createEffect(() => this.actions$.pipe(
    ofType(UserActions.loadUser),
    switchMap(() =>
      this.userService.getUser().pipe(
        map(user => {
          this.translate.use(user.locale.code);
          return UserActions.setUser({ user });
        }),
        catchError(error => of(UserActions.userError({ error })))
      )
    )
  ));

  updateUserSuccess = createEffect(() => this.actions$.pipe(
    ofType(UserActions.updateUserSuccess),
    tap((action) => {
      this.storageService.set('canLoadFromToken', false);
      this.translate.use(action.user.locale.code);
    })
  ), { dispatch: false });
}
