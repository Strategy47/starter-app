import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { StorageService } from './storage.service';
import { from, Observable, of, lastValueFrom } from 'rxjs';
import { catchError, map, switchMap } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { LocaleInterface } from '../../shared/interfaces/locale.interface';

@Injectable({
  providedIn: 'root'
})
export class LocaleService {
  private localesStorageKey = 'locales';
  constructor(private http: HttpClient, private storageService: StorageService) { }

  getLocales(): Observable<LocaleInterface[]> {
    return from(this.storageService.get(this.localesStorageKey)).pipe(
      switchMap((locales: LocaleInterface[] | null) => {
        if (locales) {
          return of(locales);
        } else {
          return this.http.get<any>(`${environment.apiUrl}/locales`).pipe(
            catchError(error => {
              console.error('Failed to fetch locales:', error);
              return of([]);
            }),
            map(response => {
              const locales = response['hydra:member'] as LocaleInterface[];
              this.storageService.set(this.localesStorageKey, locales);
              return locales;
            })
          );
        }
      })
    );
  }

  async getLocaleByIri(iri: string): Promise<LocaleInterface | undefined> {
    const locales = await lastValueFrom(this.getLocales());
    return locales.find(locale => locale['@id'] === iri);
  }
}
