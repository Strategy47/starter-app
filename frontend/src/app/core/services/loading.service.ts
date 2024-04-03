import { Injectable } from '@angular/core';
import { LoadingController } from '@ionic/angular';

@Injectable({
  providedIn: 'root'
})
export class LoadingService {

  private loadingInstance: HTMLIonLoadingElement | null = null;

  constructor(
    private loadingController: LoadingController,
    ) {
  }


  async present(message: string) {
      if (!this.loadingInstance) {
        const loading = await this.loadingController.create({
          message: message,
        });
        await loading.present();

        this.loadingInstance = loading;

        setTimeout(() => {
          console.log('okkk');
          this.dismiss();
        }, 30000);
      }
  }

  async dismiss() {
    if (this.loadingInstance) {
      await this.loadingInstance.dismiss();
      this.loadingInstance = null;
    }
  }
}
