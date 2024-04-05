import { Injectable } from '@angular/core';
import { jwtDecode } from 'jwt-decode';
import { BehaviorSubject, Observable } from 'rxjs';
import { StorageService } from './storage.service';
import { environment } from '../../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { SignInInterface, SignUpInterface } from '../../shared/interfaces/auth.interface';
import { UserInterface } from '../../shared/interfaces/user.interface';
import { catchError, map } from 'rxjs/operators';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {
  constructor(
    private storageService:StorageService,
    private http: HttpClient,
    private router: Router,
    ) {
  }

  logIn(credentials: SignInInterface): Observable<any> {
    const url = `${environment.apiUrl}/authenticate`;
    const { identifier, phone, password } = credentials;
    return this.http.post<UserInterface>(url, { identifier, phone, password });
  }

  register(userData: SignUpInterface): Observable<UserInterface> {
    return this.http.post<UserInterface>(`${environment.apiUrl}/register`, userData).pipe(
      map(response => response),
      catchError(error => {
        throw error;
      })
    );
  }

  public isTokenExpired(token: string): boolean
  {
    const decodedToken: { [key: string]: any } = jwtDecode(token);
    let expiry = decodedToken["exp"];
    let isExpired = ((Math.floor((new Date).getTime() / 1000)) >= expiry);

    if (isExpired) {
      this.storageService.remove('token')
    }

    return isExpired;
  }

  async isAuthenticatedUser(): Promise<boolean>
  {
    const token = await this.storageService.get("token");

    if (!token)
      return false;

    return !this.isTokenExpired(token);
  }

  public redirectUserByRole(roles: string[]): void {
    if (roles.includes('ROLE_ADMIN')) {
      this.router.navigateByUrl('/admin', { replaceUrl: true });
    } else if (roles.includes('ROLE_OWNER')) {
      this.router.navigateByUrl('/owner', { replaceUrl: true });
    } else if (roles.includes('ROLE_TENANT')) {
      this.router.navigateByUrl('/tenant', { replaceUrl: true });
    } else if (roles.includes('ROLE_AGENCY')) {
      this.router.navigateByUrl('/agency', { replaceUrl: true });
    } else {
      this.router.navigateByUrl('/auth', { replaceUrl: true });
    }
  }
}
