import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ArtistService } from 'app/modules/artist/artist.service';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatTableModule } from '@angular/material/table';

@Component({
    selector     : 'artist-dashboard',
    standalone   : true,
    templateUrl  : './dashboard.component.html',
    imports      : [CommonModule, MatIconModule, MatButtonModule, MatTableModule],
    encapsulation: ViewEncapsulation.None
})
export class DashboardComponent implements OnInit
{
    stats: any;
    recentSongs: any[] = [];
    displayedColumns: string[] = ['title', 'status', 'price', 'genre'];

    constructor(private _artistService: ArtistService) {}

    ngOnInit(): void
    {
        this._artistService.getDashboardStats().subscribe((data) => {
            this.stats = data;
            this.recentSongs = data.recent_songs;
        });
    }
}
