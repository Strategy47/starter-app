import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { AddressInterface } from '../../interfaces/address.interface';
import { CountryInterface } from '../../interfaces/country.interface';
import { CountryService } from '../../../core/services/country.service';

@Component({
  selector: 'app-address-form',
  templateUrl: './address-form.component.html',
  styleUrls: ['./address-form.component.scss'],
})
export class AddressFormComponent  implements OnInit {
  @Output() addressChanged = new EventEmitter<AddressInterface>();
  addressForm!: FormGroup;
  countries: CountryInterface[] = [];
  loadingCountries = true;

  constructor(
    private fb: FormBuilder,
    private countryService: CountryService
  ) {}

  ngOnInit() {
    this.loadCountries();

    this.addressForm = this.fb.group({
      address: ['', Validators.required],
      addressSupplement: [''],
      zipCode: ['', Validators.required],
      city: ['', Validators.required],
      country: ['', Validators.required],
    });

    this.addressForm.valueChanges.subscribe((value) => {
      this.addressChanged.emit(value as AddressInterface);
    });
  }

  async loadCountries() {
    try {
      const countriesData = await this.countryService.getCountries().toPromise();
      this.countries = countriesData || [];
    } catch (error) {
      console.error('Error loading countries:', error);
    }
    this.loadingCountries = false;
  }
}
