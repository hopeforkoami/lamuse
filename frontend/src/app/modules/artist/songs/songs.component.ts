import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ArtistService } from 'app/modules/artist/artist.service';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatTableModule } from '@angular/material/table';
import { MatDialogModule, MatDialog } from '@angular/material/dialog';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';

@Component({
    selector     : 'artist-songs',
    standalone   : true,
    templateUrl  : './songs.component.html',
    imports      : [
        CommonModule, MatIconModule, MatButtonModule, MatTableModule,
        MatDialogModule, FormsModule, ReactiveFormsModule, MatFormFieldModule,
        MatInputModule, MatSelectModule
    ],
    encapsulation: ViewEncapsulation.None
})
export class SongsComponent implements OnInit
{
    songs: any[] = [];
    displayedColumns: string[] = ['title', 'status', 'price', 'genre', 'actions'];
    showUploadForm = false;
    songForm: FormGroup;

    constructor(
        private _artistService: ArtistService,
        private _formBuilder: FormBuilder
    ) {
        this.songForm = this._formBuilder.group({
            title: ['', Validators.required],
            price: [0, [Validators.required, Validators.min(0)]],
            currency_code: ['XOF', Validators.required],
            genre: ['', Validators.required],
            s3_key_main: [''],
            s3_key_teaser: ['']
        });
    }

    ngOnInit(): void
    {
        this.loadSongs();
    }

    loadSongs(): void
    {
        this._artistService.getSongs().subscribe((data) => {
            this.songs = data;
        });
    }

    toggleUploadForm(): void
    {
        this.showUploadForm = !this.showUploadForm;
    }

    onSubmit(): void
    {
        if (this.songForm.invalid) return;

        this._artistService.uploadSong(this.songForm.value).subscribe(() => {
            this.loadSongs();
            this.showUploadForm = false;
            this.songForm.reset({currency_code: 'XOF', price: 0});
        });
    }
}
