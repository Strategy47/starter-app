import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class NavbarService {

  constructor() { }

  private selectedSegmentSource = new BehaviorSubject<string>('signIn');
  currentSelectedSegment = this.selectedSegmentSource.asObservable();

  changeSelectedSegment(segment: string) {
    this.selectedSegmentSource.next(segment);
  }
}
