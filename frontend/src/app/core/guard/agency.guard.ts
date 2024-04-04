import { Injectable } from '@angular/core';
import {RoleGuard} from "./role.guard";

@Injectable({
  providedIn: 'root'
})
export class AgencyGuard extends RoleGuard {

  protected checkRole(roles: string[] | null): boolean {
    return roles !== null && roles.includes('ROLE_AGENCY');
  }
}
