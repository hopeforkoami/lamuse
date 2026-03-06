import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSelectModule } from '@angular/material/select';
import { MatFormFieldModule } from '@angular/material/form-field';
import { AdminService } from 'app/modules/admin/admin.service';

@Component({
    selector: 'app-artists',
    standalone: true,
    imports: [CommonModule, MatTableModule, MatButtonModule, MatIconModule, MatSelectModule, MatFormFieldModule],
    templateUrl: './artists.component.html'
})
export class ArtistsComponent implements OnInit {
    private _adminService = inject(AdminService);
    artists: any[] = [];
    displayedColumns: string[] = ['name', 'email', 'country', 'star_ranking', 'actions'];
    starLevels = [1, 2, 3, 4, 5];

    ngOnInit(): void {
        this.loadArtists();
    }

    loadArtists(): void {
        this._adminService.getArtists().subscribe(data => {
            this.artists = data;
        });
    }

    updateStar(artistId: number, starRanking: number): void {
        this._adminService.updateArtistStar(artistId, starRanking).subscribe(() => {
            this.loadArtists();
        });
    }
}
