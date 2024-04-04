import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of, from } from 'rxjs';
import { catchError, map, switchMap } from 'rxjs/operators';
import { StorageService } from './storage.service';
import { CountryInterface } from '../../shared/interfaces/country.interface';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class CountryService {
  private countriesStorageKey = 'countries';

  constructor(private http: HttpClient, private storageService: StorageService) { }

  getCountries(): Observable<CountryInterface[]> {
    return from(this.storageService.get(this.countriesStorageKey)).pipe(
      switchMap((countries: CountryInterface[] | null) => {
        if (countries) {
          return of(countries);
        } else {
          return this.http.get<any>(`${environment.apiUrl}/countries`).pipe(
            catchError(error => {
              console.error('Failed to fetch countries:', error);
              return of([]);
            }),
            map(response => {
              const countries = response['hydra:member'] as CountryInterface[];
              this.storageService.set(this.countriesStorageKey, countries);
              return countries;
            })
          );
        }
      })
    );
  }
}
