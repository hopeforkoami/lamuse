import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({providedIn: 'root'})
export class AdminService
{
    private _httpClient = inject(HttpClient);

    /**
     * Get dashboard stats
     */
    getDashboardStats(): Observable<any>
    {
        return this._httpClient.get('api/admin/dashboard-stats');
    }

    /**
     * Get artists
     */
    getArtists(): Observable<any[]>
    {
        return this._httpClient.get<any[]>('api/admin/artists');
    }

    /**
     * Update artist star ranking
     */
    updateArtistStar(artistId: number, starRanking: number): Observable<any>
    {
        return this._httpClient.post(`api/admin/artists/${artistId}/star`, { star_ranking: starRanking });
    }

    /**
     * Get pricing rules
     */
    getPricingRules(): Observable<any[]>
    {
        return this._httpClient.get<any[]>('api/admin/pricing-rules');
    }

    /**
     * Upsert pricing rule
     */
    upsertPricingRule(rule: any): Observable<any>
    {
        return this._httpClient.post('api/admin/pricing-rules', rule);
    }

    /**
     * Get payment health status
     */
    getPaymentHealth(): Observable<any[]>
    {
        return this._httpClient.get<any[]>('api/admin/payment-health');
    }

    /**
     * Get all songs
     */
    getSongs(): Observable<any[]>
    {
        return this._httpClient.get<any[]>('api/admin/songs');
    }

    /**
     * Update song status (publish, draft, archive)
     */
    updateSongStatus(songId: number, status: string): Observable<any>
    {
        return this._httpClient.post(`api/admin/songs/${songId}/status`, { status });
    }

    /**
     * Get all orders
     */
    getOrders(): Observable<any[]>
    {
        return this._httpClient.get<any[]>('api/admin/orders');
    }

    /**
     * Get all reports
     */
    getReports(): Observable<any[]>
    {
        return this._httpClient.get<any[]>('api/admin/reports');
    }
}
