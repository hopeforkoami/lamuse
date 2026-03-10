import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({providedIn: 'root'})
export class AdminService
{
    private _httpClient = inject(HttpClient);

    /**
     * Get dashboard stats
     */
    getDashboardStats(): Observable<any>
    {
        return this._httpClient.get(`${environment.apiUrl}/admin/dashboard-stats`);
    }

    /**
     * Get artists
     */
    getArtists(): Observable<any[]>
    {
        return this._httpClient.get<any[]>(`${environment.apiUrl}/admin/artists`);
    }

    /**
     * Update artist star ranking
     */
    updateArtistStar(artistId: number, starRanking: number): Observable<any>
    {
        return this._httpClient.post(`${environment.apiUrl}/admin/artists/${artistId}/star`, { star_ranking: starRanking });
    }

    /**
     * Get pricing rules
     */
    getPricingRules(): Observable<any[]>
    {
        return this._httpClient.get<any[]>(`${environment.apiUrl}/admin/pricing-rules`);
    }

    /**
     * Upsert pricing rule
     */
    upsertPricingRule(rule: any): Observable<any>
    {
        return this._httpClient.post(`${environment.apiUrl}/admin/pricing-rules`, rule);
    }

    /**
     * Get payment health status
     */
    getPaymentHealth(): Observable<any[]>
    {
        return this._httpClient.get<any[]>(`${environment.apiUrl}/admin/payment-health`);
    }

    /**
     * Get all songs
     */
    getSongs(): Observable<any[]>
    {
        return this._httpClient.get<any[]>(`${environment.apiUrl}/admin/songs`);
    }

    /**
     * Update song status (publish, draft, archive)
     */
    updateSongStatus(songId: number, status: string): Observable<any>
    {
        return this._httpClient.post(`${environment.apiUrl}/admin/songs/${songId}/status`, { status });
    }

    /**
     * Get all orders
     */
    getOrders(): Observable<any[]>
    {
        return this._httpClient.get<any[]>(`${environment.apiUrl}/admin/orders`);
    }

    /**
     * Get all reports
     */
    getReports(): Observable<any[]>
    {
        return this._httpClient.get<any[]>(`${environment.apiUrl}/admin/reports`);
    }
}
