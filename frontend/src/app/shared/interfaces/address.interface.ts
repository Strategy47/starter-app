import { CountryInterface } from './country.interface';

export interface AddressInterface {
  id?: number;
  address: string;
  zipCode: string;
  city: string;
  addressSupplement: string  | null;
  country: string;
}
