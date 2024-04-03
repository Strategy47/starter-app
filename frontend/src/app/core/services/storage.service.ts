import { Injectable } from '@angular/core';
import { Storage } from '@ionic/storage-angular';

@Injectable({
  providedIn: 'root'
})
export class StorageService {

  constructor(private storage: Storage) {
    this.initStorage();
  }

  private async initStorage(): Promise<void> {
    await this.storage.create();
  }

  async set(key: string, value: any): Promise<boolean> {
    await this.storage.set(key, value);

    return true;
  }
  async get(key: string): Promise<any> {
    const value = await this.storage.get(key);
    return value;
  }

  async remove(key: string): Promise<any> {
    return await this.storage.remove(key);
  }
}
