import { Routes } from '@angular/router';
import { ArtistsComponent } from 'app/modules/admin/artists/artists.component';
import { PricingComponent } from 'app/modules/admin/pricing/pricing.component';
import { PaymentHealthComponent } from 'app/modules/admin/payment-health/payment-health.component';

export default [
    {
        path     : 'artists',
        component: ArtistsComponent
    },
    {
        path     : 'pricing',
        component: PricingComponent
    },
    {
        path     : 'payment-health',
        component: PaymentHealthComponent
    },
] as Routes;
