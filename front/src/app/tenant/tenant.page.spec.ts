import { ComponentFixture, TestBed } from '@angular/core/testing';
import { TenantPage } from './tenant.page';

describe('TenantPage', () => {
  let component: TenantPage;
  let fixture: ComponentFixture<TenantPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(TenantPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
