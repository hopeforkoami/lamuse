import { CommonModule } from '@angular/common';
import { Component, OnInit, inject } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatMenuModule } from '@angular/material/menu';
import { MatTableModule } from '@angular/material/table';
import { AdminService } from 'app/modules/admin/admin.service';

@Component({
    selector: 'admin-songs',
    standalone: true,
    imports: [CommonModule, MatTableModule, MatButtonModule, MatIconModule, MatMenuModule],
    template: `
        <div class="flex flex-col flex-auto min-w-0">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row flex-0 sm:items-center sm:justify-between p-6 sm:py-8 sm:px-10 border-b bg-card dark:bg-transparent">
                <div class="flex-auto">
                    <div class="text-3xl font-extrabold tracking-tight">Songs Management</div>
                    <div class="flex items-center mt-0.5 font-medium text-secondary">
                        Manage all songs uploaded to the platform.
                    </div>
                </div>
            </div>

            <!-- Main -->
            <div class="flex-auto p-6 sm:p-10">
                <div class="bg-card shadow rounded-2xl overflow-hidden">
                    <table mat-table [dataSource]="songs" class="w-full">
                        <!-- ID -->
                        <ng-container matColumnDef="id">
                            <th mat-header-cell *matHeaderCellDef>ID</th>
                            <td mat-cell *matCellDef="let song">{{song.id}}</td>
                        </ng-container>

                        <!-- Title -->
                        <ng-container matColumnDef="title">
                            <th mat-header-cell *matHeaderCellDef>Title</th>
                            <td mat-cell *matCellDef="let song">
                                <span class="font-bold">{{song.title}}</span>
                            </td>
                        </ng-container>

                        <!-- Artist -->
                        <ng-container matColumnDef="artist">
                            <th mat-header-cell *matHeaderCellDef>Artist</th>
                            <td mat-cell *matCellDef="let song">{{song.artist?.name}}</td>
                        </ng-container>

                        <!-- Price -->
                        <ng-container matColumnDef="price">
                            <th mat-header-cell *matHeaderCellDef>Price</th>
                            <td mat-cell *matCellDef="let song">{{song.price}} {{song.currency_code}}</td>
                        </ng-container>

                        <!-- Status -->
                        <ng-container matColumnDef="status">
                            <th mat-header-cell *matHeaderCellDef>Status</th>
                            <td mat-cell *matCellDef="let song">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                      [ngClass]="{
                                          'bg-green-100 text-green-800': song.status === 'published',
                                          'bg-yellow-100 text-yellow-800': song.status === 'draft',
                                          'bg-gray-100 text-gray-800': song.status === 'archived'
                                      }">
                                    {{song.status | uppercase}}
                                </span>
                            </td>
                        </ng-container>

                        <!-- Actions -->
                        <ng-container matColumnDef="actions">
                            <th mat-header-cell *matHeaderCellDef>Actions</th>
                            <td mat-cell *matCellDef="let song">
                                <button mat-icon-button [matMenuTriggerFor]="menu">
                                    <mat-icon>more_vert</mat-icon>
                                </button>
                                <mat-menu #menu="matMenu">
                                    <button mat-menu-item (click)="updateStatus(song, 'published')" *ngIf="song.status !== 'published'">
                                        <mat-icon>publish</mat-icon>
                                        <span>Publish</span>
                                    </button>
                                    <button mat-menu-item (click)="updateStatus(song, 'draft')" *ngIf="song.status !== 'draft'">
                                        <mat-icon>edit_note</mat-icon>
                                        <span>Set as Draft</span>
                                    </button>
                                    <button mat-menu-item (click)="updateStatus(song, 'archived')" *ngIf="song.status !== 'archived'">
                                        <mat-icon>archive</mat-icon>
                                        <span>Archive</span>
                                    </button>
                                </mat-menu>
                            </td>
                        </ng-container>

                        <tr mat-header-row *matHeaderRowDef="displayedColumns"></tr>
                        <tr mat-row *matRowDef="let row; columns: displayedColumns;"></tr>
                    </table>
                </div>
            </div>
        </div>
    `
})
export class SongsComponent implements OnInit {
    private _adminService = inject(AdminService);

    songs: any[] = [];
    displayedColumns: string[] = ['id', 'title', 'artist', 'price', 'status', 'actions'];

    ngOnInit(): void {
        this.loadSongs();
    }

    loadSongs(): void {
        this._adminService.getSongs().subscribe(songs => {
            this.songs = songs;
        });
    }

    updateStatus(song: any, status: string): void {
        this._adminService.updateSongStatus(song.id, status).subscribe(() => {
            this.loadSongs();
        });
    }
}
