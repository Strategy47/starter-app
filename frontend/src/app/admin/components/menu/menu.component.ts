import { Component, Input, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';;
import { Observable, of } from 'rxjs';
import * as authActions from '../../../store/auth/auth.actions';
import { UserInterface } from '../../../shared/interfaces/user.interface';

@Component({
  selector: 'app-admin-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss'],
})
export class MenuComponent {
  @Input() user$: Observable<UserInterface | null> = of(null)

  constructor(
    private store: Store
  ) {
  }

  appPages = [
    {
      title: 'menu.home',
      url: '/admin/home',
      icon: 'calendar'
    }
  ];

  logout()
  {
    this.store.dispatch(authActions.logout());
  }
}
