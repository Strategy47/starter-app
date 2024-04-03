import { Component, OnDestroy, OnInit } from '@angular/core';
import { AbstractControl, FormControl, FormGroup, Validators} from '@angular/forms';
import { Store } from '@ngrx/store';
import * as fromAuthAction from '../store/auth/auth.actions';
import * as authSelector from '../store/auth/auth.selectors';
import { SignInInterface, SignUpInterface } from '../shared/interfaces/auth.interface';
import { NavbarService } from '../core/services/navbar.service';
import { map, Observable, startWith, Subscription, tap } from 'rxjs';
import { LoadingService } from '../core/services/loading.service';
import { AddressValidator } from '../shared/components/address-form/address-validator';
import { LocaleService } from '../core/services/locale.service';
import { LocaleInterface } from '../shared/interfaces/locale.interface';

@Component({
  selector: 'app-auth',
  templateUrl: './auth.page.html',
  styleUrls: ['./auth.page.scss'],
})
export class AuthPage implements OnInit, OnDestroy {
  public selectedSegment: string = 'sign-in'

  subscriptions: Subscription[] = [];
  public errorMsg: any;
  locales: LocaleInterface[] = [];
  loadingLocales = true;

  signInForm = new FormGroup({
    identifier: new FormControl("", [Validators.required, this.phoneOrEmailValidator]),
    password: new FormControl("", Validators.required),
  });

  rolePreferenceCtrl = new FormControl("", Validators.required);
  agencyCtrl = new FormGroup({
    name: new FormControl("", [Validators.required]),
    siret: new FormControl("", Validators.required),
  });

  signUpForm = new FormGroup({
    role: this.rolePreferenceCtrl,
    firstname: new FormControl("", [Validators.required]),
    lastname: new FormControl("", [Validators.required]),
    email: new FormControl("", [Validators.required,Validators.email]),
    locale: new FormControl("", [Validators.required]),
    phone: new FormControl("", [Validators.required, Validators.minLength(10), Validators.maxLength(15)]),
    password: new FormControl("", Validators.required),
    address: new FormControl("", [AddressValidator]),
  });

  showAgencyCtrl$!: Observable<boolean>;

  constructor(private loadingService: LoadingService,
              private navbarService: NavbarService,
              private store: Store,
              private localeService: LocaleService,
  ) {
    this.navbarService.currentSelectedSegment.subscribe(segment => {
      this.selectedSegment = segment;
    });
  }

  ngOnInit() {
    this.loadLocales();

    this.subscriptions.push(
      this.store.select(authSelector.selectError).subscribe(error => (this.errorMsg = error))
    );

    this.store.select(authSelector.selectIsLoading).subscribe(isLoading => {
      if (isLoading) {
        this.loadingService.present('Chargement en cours...');
      } else {
        this.loadingService.dismiss();
      }
    });

    this.showAgencyCtrl$ = this.rolePreferenceCtrl.valueChanges.pipe(
      startWith(this.rolePreferenceCtrl.value),
      map(preference => preference === 'agency'),
      tap(showAgencyCtrl => this.setAgencyValidators(showAgencyCtrl))
    );
  }

  ngOnDestroy() {
    this.subscriptions.forEach(subscription => subscription.unsubscribe());
  }

  async onSubmit() {
    let body: SignInInterface = {
      identifier: '',
      password: this.signInForm.value.password as string,
      phone: ''
    }

    // check identifier is email or phone or username. and delete the rest
    const identifier = this.signInForm.value.identifier as string;
    if (identifier.includes('@')) {
      body.identifier = identifier;
      delete body.phone;
    } else if (identifier.match(/^[0-9]+$/) != null) {
      body.phone = identifier;
      delete body.identifier;
    } else {
      this.store.dispatch(fromAuthAction.loginFailure({error: 'invalid_email_or_phone'}));
      return;
    }

    this.store.dispatch(fromAuthAction.login(body));
  }

  onAddressChanged(address: any) {
    this.signUpForm.patchValue({ address: address });
  }

  onSubmitSignUp() {
    let formData =  this.signUpForm.value;

    let body = formData as SignUpInterface;

    if (body.role) {
      body.roles = [body.role];
    }

    this.store.dispatch(fromAuthAction.register(body));
  }

  phoneOrEmailValidator(control: AbstractControl) {
    const name = control.value;
    const phoneRegex = /^\d{10}$/;

    if (name.includes('@')) {
      return null;
    }

    if (!phoneRegex.test(name)) {
      //   return { invalidName: true };
    }

    return null;
  }

  private setAgencyValidators(showAgencyCtrl: boolean) {
    if (showAgencyCtrl) {
      this.agencyCtrl.addValidators([
        Validators.required,
        Validators.email
      ]);
    } else {
      this.agencyCtrl.clearValidators();
    }
    this.agencyCtrl.updateValueAndValidity();
  }

  async loadLocales() {
    try {
      const localeData = await this.localeService.getLocales().toPromise();
      this.locales = localeData || [];
      console.log(this.locales)
    } catch (error) {
      console.error('Error loading countries:', error);
    }
    this.loadingLocales = false;
  }
}
