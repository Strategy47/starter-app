import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AdminGuard } from './core/guard/admin.guard';
import { AgencyGuard } from './core/guard/agency.guard';
import { OwnerGuard } from './core/guard/owner.guard';
import { TenantGuard } from './core/guard/tenant.guard';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'auth',
    pathMatch: 'full'
  },
  {
    path: 'auth',
    loadChildren: () => import('./auth/auth.module').then( m => m.AuthPageModule)
  },
  {
    path: 'not-found',
    loadChildren: () => import('./not-found/not-found.module').then( m => m.NotFoundPageModule)
  },
  {
    path: 'admin',
    loadChildren: () => import('./admin/admin.module').then( m => m.AdminPageModule),
    canActivate: [AdminGuard]
  },
  {
    path: 'agency',
    loadChildren: () => import('./agency/agency.module').then( m => m.AgencyPageModule),
    canActivate: [AgencyGuard]
  },
  {
    path: 'tenant',
    loadChildren: () => import('./tenant/tenant.module').then( m => m.TenantPageModule),
    canActivate: [TenantGuard]
  },
  {
    path: 'owner',
    loadChildren: () => import('./owner/owner.module').then( m => m.OwnerPageModule),
    canActivate: [OwnerGuard]
  },
  {
    path: '404',
    loadChildren: () => import('./not-found/not-found.module').then( m => m.NotFoundPageModule)
  },
  {path: '**', redirectTo: '/404'},
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
