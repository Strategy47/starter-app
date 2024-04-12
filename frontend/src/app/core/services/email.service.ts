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
    return this.http.post(`${this.baseUrl}/forgot_password/`, {email}, { headers: this.header})
  }

  verifyEmailAccount(emailToken: string) {
    return this.http.post(`${this.baseUrl}/mail/verify?token=${emailToken}`, { headers: this.header})
  }

  changePassword(id: string, body: any){
    return this.http.patch(`${this.baseUrl}/account/forgot-password/${id}`, body, { headers: this.header})
  }
}
