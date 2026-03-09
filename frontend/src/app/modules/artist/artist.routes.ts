import { Routes } from '@angular/router';
import { DashboardComponent } from 'app/modules/artist/dashboard/dashboard.component';
import { SongsComponent } from 'app/modules/artist/songs/songs.component';
import { ProfileComponent } from 'app/modules/artist/profile/profile.component';
import { SalesComponent } from 'app/modules/artist/sales/sales.component';

export const artistRoutes: Routes = [
    {
        path      : '',
        redirectTo: 'dashboard',
        pathMatch : 'full'
    },
    {
        path     : 'dashboard',
        component: DashboardComponent
    },
    {
        path     : 'songs',
        component: SongsComponent
    },
    {
        path     : 'profile',
        component: ProfileComponent
    },
    {
        path     : 'sales',
        component: SalesComponent
    }
];
