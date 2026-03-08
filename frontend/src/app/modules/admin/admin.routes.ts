import { Routes } from '@angular/router';
import { ArtistsComponent } from 'app/modules/admin/artists/artists.component';
import { PricingComponent } from 'app/modules/admin/pricing/pricing.component';
import { PaymentHealthComponent } from 'app/modules/admin/payment-health/payment-health.component';
import { SongsComponent } from 'app/modules/admin/songs/songs.component';
import { ExampleComponent } from 'app/modules/admin/example/example.component';

export default [
    {
        path     : '',
        pathMatch: 'full',
        redirectTo: 'dashboard'
    },
    {
        path     : 'dashboard',
        component: ExampleComponent
    },
    {
        path     : 'artists',
        component: ArtistsComponent
    },
    {
        path     : 'songs',
        component: SongsComponent
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
