import { Injectable } from '@angular/core';
import { HttpHeaders, HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { catchError, tap, map } from 'rxjs/operators';
import { Store } from '@ngrx/store';
import { updateUserSuccess, updateUserFailure } from '../../store/user/user.actions';
import { environment } from '../../../environments/environment';
import { UserInterface } from '../../shared/interfaces/user.interface';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  constructor(
    private httpClient: HttpClient,
    private store: Store
  ) {}

  getUser(): Observable<UserInterface> {
    return this.httpClient.get<any>(`${environment.apiUrl}/users/me`).pipe(
      map(response => response['hydra:member'] ? response['hydra:member'][0] : response)
    );
  }

  updateUser(userId: any, userData: Partial<UserInterface>): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/merge-patch+json'
    });

    return this.httpClient.patch<UserInterface>(`${environment.apiUrl}/users/${userId}`, userData, { headers }).pipe(
      tap((updatedUser: UserInterface) => this.store.dispatch(updateUserSuccess({ user: updatedUser }))),
      catchError(error => {
        this.store.dispatch(updateUserFailure({ error }));
        throw error;
      })
    );
  }

  postUser(userData: UserInterface): Observable<UserInterface> {
    return this.httpClient.post<UserInterface>(`${environment.apiUrl}/users`, userData).pipe(
      map(response => response),
      catchError(error => {
        throw error;
      })
    );
  }
}
