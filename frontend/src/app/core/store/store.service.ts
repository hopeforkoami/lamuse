import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({
    providedIn: 'root'
})
export class StoreService {
    private apiUrl = environment.apiUrl;

    constructor(private _httpClient: HttpClient) {}

    /**
     * Get all published songs
     */
    getSongs(): Observable<any[]> {
        return this._httpClient.get<any[]>(`${this.apiUrl}/songs`);
    }

    /**
     * Get song by ID
     */
    getSong(id: number): Observable<any> {
        return this._httpClient.get<any>(`${this.apiUrl}/songs/${id}`);
    }

    /**
     * Create a new order (checkout)
     * @param orderData { song_ids: number[], email?: string, payment_provider: string }
     */
    checkout(orderData: any): Observable<any> {
        return this._httpClient.post<any>(`${this.apiUrl}/checkout`, orderData);
    }

    /**
     * Get buyer's library (authenticated)
     */
    getLibrary(): Observable<any[]> {
        return this._httpClient.get<any[]>(`${this.apiUrl}/buyer/library`);
    }

    /**
     * Get download URL for a song (authenticated)
     */
    getDownloadUrl(songId: number): Observable<{ download_url: string }> {
        return this._httpClient.get<{ download_url: string }>(`${this.apiUrl}/buyer/download/${songId}`);
    }
}
