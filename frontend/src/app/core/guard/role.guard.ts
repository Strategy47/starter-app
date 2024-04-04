import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import * as userSelectors from '../../store/user/user.selectors';
import { tap, map } from 'rxjs/operators';
import { Store } from '@ngrx/store';

@Injectable({
  providedIn: 'root'
})
export abstract class RoleGuard {

  constructor(
    private router: Router,
    private store: Store
  ) {}

  canActivate(): Observable<boolean> | Promise<boolean> | boolean {

    return this.store.select(userSelectors.selectRoles).pipe(
      map(roles => this.checkRole(roles)),
      tap(hasRole => {
        if (!hasRole) {
          this.router.navigateByUrl('/auth');
        }
      })
    );
  }

  protected abstract checkRole(roles: string[] | null): boolean;
}
