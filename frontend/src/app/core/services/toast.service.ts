import { Injectable } from '@angular/core';
import { ToastController } from '@ionic/angular';

@Injectable({
  providedIn: 'root'
})
export class ToastService {

  constructor(private toastController: ToastController) { }

  async presentToast(message: string, duration: number = 2000, color: string) {
    const toast = await this.toastController.create({
      message,
      duration,
      cssClass: color
    });
    await toast.present();
  }
}
