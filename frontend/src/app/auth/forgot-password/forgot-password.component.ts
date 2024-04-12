import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { EmailService } from '../../core/services/email.service';
import { LoadingController } from '@ionic/angular';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.scss'],
})
export class ForgotPasswordComponent implements OnInit {

  verifySuccess = false
  errorVerify = false;
  constructor(private router: Router,
              private emailService: EmailService,
              private loadingCtrl: LoadingController) { }

  ngOnInit() {}

  emailForm = new FormGroup({
    email: new FormControl("", Validators.required),
  })

  async verifyEmail() {
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...',
    });
    await loading.present();

    this.emailService.sendResetPasswordEmail(this.emailForm.value.email as string).subscribe(
      async () => {
        await loading.dismiss();
        this.verifySuccess = true;
      },
      async error => {
        await loading.dismiss();
        this.errorVerify = true;
      }
    );
  }


  async loadingRedirect(){
    const loading = await this.loadingCtrl.create({
      message: 'Redirect you to next step...',
      duration: 2000
    });
    await loading.present();
  }

  async nextStep(){
    await this.loadingRedirect();
    setTimeout(()=> {
      this.router.navigate(['/auth']);
    }, 2000)
  }
}
