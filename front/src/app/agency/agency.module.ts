import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { AgencyPageRoutingModule } from './agency-routing.module';

import { AgencyPage } from './agency.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    AgencyPageRoutingModule
  ],
  declarations: [AgencyPage]
})
export class AgencyPageModule {}
