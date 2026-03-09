import { Routes } from '@angular/router';
import { LandingHomeComponent } from 'app/modules/landing/home/home.component';
import { LandingCheckoutComponent } from 'app/modules/landing/checkout/checkout.component';

export default [
    {
        path     : '',
        component: LandingHomeComponent,
    },
    {
        path     : 'checkout',
        component: LandingCheckoutComponent,
    },
] as Routes;
