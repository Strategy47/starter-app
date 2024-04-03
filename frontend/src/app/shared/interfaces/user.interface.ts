import { AddressInterface } from './address.interface';
import { LocaleInterface } from './locale.interface';


export interface UserInterface {
  '@id'?: string;
  id?: number;
  firstname: string;
  lastname: string;
  email: string;
  roles: string[];
  address: AddressInterface;
  phone: string;
  locale: LocaleInterface;
  lastLoginAt?: string;
  createdAt?: string;
  updatedAt?: string;
  password?: string;
  token?: string;
  agency: {
    id: number;
    name: string;
    address: {
      id: number;
    };
  };
  adminAgency: boolean;
}
