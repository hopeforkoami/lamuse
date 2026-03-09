import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTableModule } from '@angular/material/table';
import { StoreService } from 'app/core/store/store.service';

@Component({
    selector     : 'buyer-library',
    templateUrl  : './library.component.html',
    encapsulation: ViewEncapsulation.None,
    standalone   : true,
    imports      : [CommonModule, MatButtonModule, MatIconModule, MatTableModule],
})
export class BuyerLibraryComponent implements OnInit
{
    songs: any[] = [];
    displayedColumns: string[] = ['title', 'artist', 'actions'];

    constructor(private _storeService: StoreService)
    {
    }

    ngOnInit(): void {
        this._storeService.getLibrary().subscribe((data) => {
            this.songs = data;
        });
    }

    download(songId: number): void {
        this._storeService.getDownloadUrl(songId).subscribe((response) => {
            window.open(response.download_url, '_blank');
        });
    }
}
