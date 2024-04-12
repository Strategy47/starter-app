import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { LoadingController } from '@ionic/angular';
import { ToastService } from '../../core/services/toast.service';
import { ActivatedRoute } from '@angular/router';
import { UserService } from '../../core/services/user.service';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.component.html',
  styleUrls: ['./reset-password.component.scss'],
})
export class ResetPasswordComponent implements OnInit {

  changePasswordSuccess = false
  errorConfirmPassword = false;
  token: string = '';

  constructor(
    private route: ActivatedRoute,
    private userService: UserService,
    private loadingCtrl: LoadingController,
    private toastService: ToastService
  ) {
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.token = params['token'];
    });
  }

  changePasswordForm = new FormGroup({
    password: new FormControl("", Validators.required),
    confirmPassword: new FormControl("", Validators.required),
  });

  async onSubmit() {
    const loading = await this.loadingCtrl.create({
      message: 'Changing password...',
    });
    await loading.present();

    if (this.changePasswordForm.value.password !== this.changePasswordForm.value.confirmPassword) {
      this.changePasswordSuccess = false;
      this.errorConfirmPassword = true
      await loading.dismiss();
    } else {
      this.userService.sendResetPassword(this.token, this.changePasswordForm.value.password as string).subscribe({
        next: (response: any) => {
          setTimeout(() => {
            loading.dismiss();
            this.changePasswordSuccess = true;
            localStorage.removeItem('id');
            localStorage.removeItem('emailVerifyToken');
          }, 2000);
        },
        error: (error: any) => {
          this.toastService.presentToast('Something wrong! Try Again', 2000, 'custom-warning-toast');
          loading.dismiss();
        }
      })
    }
  }
}
