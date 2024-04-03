import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class EmailService {
  public baseUrl = environment.apiUrl;
  header = new HttpHeaders().set('Content-Type', 'application/json');
  constructor(private http: HttpClient) { }

  sendResetPasswordEmail(email: string) {
    return this.http.post(`${this.baseUrl}/users/reset-password`, {email}, { headers: this.header})
  }
}
