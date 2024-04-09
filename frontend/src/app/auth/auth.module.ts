import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { AuthPageRoutingModule } from './auth-routing.module';
import { AuthPage } from './auth.page';
import { TranslateModule } from '@ngx-translate/core';
import { AddressFormComponent } from '../shared/components/address-form/address-form.component';
import {SharedModule} from "../shared/shared.module";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    IonicModule.forRoot(),
    AuthPageRoutingModule,
    SharedModule,
    ReactiveFormsModule,
    TranslateModule,
  ],
  declarations: [
    AuthPage
  ]
})
export class AuthPageModule {}
