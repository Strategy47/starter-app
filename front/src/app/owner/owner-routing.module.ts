import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { OwnerPage } from './owner.page';

const routes: Routes = [
  {
    path: '',
    component: OwnerPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class OwnerPageRoutingModule {}
