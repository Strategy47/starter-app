import {Component, OnDestroy, OnInit} from '@angular/core';
import {Observable, of, Subscription} from "rxjs";
import {UserInterface} from "../shared/interfaces/user.interface";
import {select, Store} from "@ngrx/store";
import * as fromAuthAction from "../store/auth/auth.actions";
import * as authSelector from "../store/auth/auth.selectors";
import * as UserSelectors from "../store/user/user.selectors";
import {filter, map} from "rxjs/operators";

@Component({
  selector: 'app-admin',
  templateUrl: './admin.page.html',
  styleUrls: ['./admin.page.scss'],
})
export class AdminPage implements OnInit, OnDestroy {
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
