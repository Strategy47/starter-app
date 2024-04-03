import { AbstractControl } from '@angular/forms';

export function AddressValidator(control: AbstractControl): { [key: string]: any } | null {
  const addressValue = control.value;

  if (!isValidAddress(addressValue)) {
    return { invalidAddress: true };
  }

  return null;
}

function isValidAddress(address: any): boolean {
  return !!address && address.address && address.city && address.zipCode && address.country;
}
