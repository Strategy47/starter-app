import { Component, OnInit, OnDestroy } from '@angular/core';
import { select, Store } from '@ngrx/store';
import * as authSelector from './store/auth/auth.selectors';
import { UserInterface } from './shared/interfaces/user.interface';
import { Observable, of, Subscription } from 'rxjs';
import * as UserSelectors from './store/user/user.selectors';
import { filter, map } from 'rxjs/operators';
import * as fromAuthAction from './store/auth/auth.actions';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
})
export class AppComponent implements OnInit, OnDestroy {
  user$: Observable<UserInterface | null> = of(null);
  public isAuthenticated: boolean = false;
  subscriptions: Subscription[] = [];

  constructor(
    private store: Store
  ) {
  }

  ngOnInit() {
    this.store.dispatch(fromAuthAction.loginFromToken());

    this.subscriptions.push(
      this.store.select(authSelector.selectIsAuthenticated).subscribe(
        isAuthenticated => (this.isAuthenticated = isAuthenticated)
      )
    );

    this.user$ = this.store.pipe(
      select(UserSelectors.selectUser),
      filter(user => user !== null),
      map(user => user as UserInterface)
    );
  }

  ngOnDestroy() {
    this.subscriptions.forEach(subscription => subscription.unsubscribe());
  }
}
