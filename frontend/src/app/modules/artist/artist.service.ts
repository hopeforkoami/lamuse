import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ArtistService {
  private apiUrl = environment.apiUrl + '/artist';

  constructor(private _httpClient: HttpClient) {}

  getDashboardStats(): Observable<any> {
    return this._httpClient.get(`${this.apiUrl}/dashboard-stats`);
  }

  getSongs(): Observable<any[]> {
    return this._httpClient.get<any[]>(`${this.apiUrl}/songs`);
  }

  uploadSong(songData: any): Observable<any> {
    return this._httpClient.post(`${this.apiUrl}/songs`, songData);
  }

  updateProfile(profileData: any): Observable<any> {
    return this._httpClient.post(`${this.apiUrl}/profile`, profileData);
  }
}
