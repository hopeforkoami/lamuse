/* eslint-disable */
import { FuseNavigationItem } from '@fuse/components/navigation';

export const defaultNavigation: FuseNavigationItem[] = [
    {
        id   : 'dashboard',
        title: 'Dashboard',
        type : 'basic',
        icon : 'heroicons_outline:chart-pie',
        link : '/admin/dashboard'
    },
    {
        id      : 'admin',
        title   : 'Super Admin',
        type    : 'group',
        icon    : 'heroicons_outline:shield-check',
        children: [
            {
                id   : 'admin.artists',
                title: 'Artists Management',
                type : 'basic',
                icon : 'heroicons_outline:users',
                link : '/admin/artists'
            },
            {
                id   : 'admin.songs',
                title: 'Songs Management',
                type : 'basic',
                icon : 'heroicons_outline:musical-note',
                link : '/admin/songs'
            },
            {
                id   : 'admin.pricing',
                title: 'Pricing Rules',
                type : 'basic',
                icon : 'heroicons_outline:currency-dollar',
                link : '/admin/pricing'
            },
            {
                id   : 'admin.health',
                title: 'Payment Health',
                type : 'basic',
                icon : 'heroicons_outline:heart',
                link : '/admin/payment-health'
            }
        ]
    },
    {
        id      : 'artist',
        title   : 'Artist Panel',
        type    : 'group',
        icon    : 'heroicons_outline:musical-note',
        children: [
            {
                id   : 'artist.dashboard',
                title: 'Dashboard',
                type : 'basic',
                icon : 'heroicons_outline:chart-pie',
                link : '/artist/dashboard'
            },
            {
                id   : 'artist.songs',
                title: 'My Songs',
                type : 'basic',
                icon : 'heroicons_outline:musical-note',
                link : '/artist/songs'
            },
            {
                id   : 'artist.profile',
                title: 'Profile Settings',
                type : 'basic',
                icon : 'heroicons_outline:cog-8-tooth',
                link : '/artist/profile'
            },
            {
                id   : 'artist.sales',
                title: 'Sales Reports',
                type : 'basic',
                icon : 'heroicons_outline:presentation-chart-line',
                link : '/artist/sales'
            }
        ]
    }
];
export const compactNavigation: FuseNavigationItem[] = [
    {
        id   : 'example',
        title: 'Example',
        type : 'basic',
        icon : 'heroicons_outline:chart-pie',
        link : '/example'
    }
];
export const futuristicNavigation: FuseNavigationItem[] = [
    {
        id   : 'example',
        title: 'Example',
        type : 'basic',
        icon : 'heroicons_outline:chart-pie',
        link : '/example'
    }
];
export const horizontalNavigation: FuseNavigationItem[] = [
    {
        id   : 'example',
        title: 'Example',
        type : 'basic',
        icon : 'heroicons_outline:chart-pie',
        link : '/example'
    }
];
