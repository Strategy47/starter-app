import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { TenantPageRoutingModule } from './tenant-routing.module';

import { TenantPage } from './tenant.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    TenantPageRoutingModule
  ],
  declarations: [TenantPage]
})
export class TenantPageModule {}
