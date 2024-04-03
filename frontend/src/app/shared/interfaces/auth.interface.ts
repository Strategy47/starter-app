import { AddressInterface } from './address.interface';

export interface SignInInterface{
  identifier?: string;
  phone?:string;
  password: string;
}

export interface SignUpInterface{
  role?: string;
  roles?: string[];
  firstname: string;
  lastname: string;
  address?: AddressInterface;
  locale: string;
  email: string;
  phone:string;
  password: string;
}
