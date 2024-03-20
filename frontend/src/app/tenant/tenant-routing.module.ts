import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { TenantPage } from './tenant.page';

const routes: Routes = [
  {
    path: '',
    component: TenantPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class TenantPageRoutingModule {}
