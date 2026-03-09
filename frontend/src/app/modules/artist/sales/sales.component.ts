import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ArtistService } from 'app/modules/artist/artist.service';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';

@Component({
    selector     : 'artist-sales',
    standalone   : true,
    templateUrl  : './sales.component.html',
    imports      : [CommonModule, MatIconModule, MatButtonModule],
    encapsulation: ViewEncapsulation.None
})
export class SalesComponent implements OnInit
{
    salesData: any[] = [
        { song: 'Song One', units: 45, total: 9000 },
        { song: 'Song Two', units: 30, total: 9000 },
        { song: 'Afrobeat Hit', units: 120, total: 24000 }
    ];

    constructor(private _artistService: ArtistService) {}

    ngOnInit(): void {}

    exportPDF(): void
    {
        // Placeholder for PDF generation
        alert('Exporting PDF report...');
    }
}
