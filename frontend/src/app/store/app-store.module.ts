import { NgModule } from '@angular/core';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule } from '@ngrx/store';
import { StoreDevtoolsModule } from '@ngrx/store-devtools';

import { AuthEffects } from './auth/auth.effects';
import { authReducer } from './auth/auth.reducer';
import { UserEffects } from './user/user.effects';
import { userReducer } from './user/user.reducer';
import { environment } from '../../environments/environment';

@NgModule({
  imports: [
    StoreModule.forRoot({}),
    StoreModule.forFeature('user', userReducer),
    StoreModule.forFeature('auth', authReducer),
    EffectsModule.forRoot([
      AuthEffects,
      UserEffects
    ]),
    !environment.production ? StoreDevtoolsModule.instrument({ maxAge: 25 }) : [],
  ]
})
export class AppStoreModule {}
