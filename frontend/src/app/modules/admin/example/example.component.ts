import { Component, inject, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AdminService } from 'app/modules/admin/admin.service';

@Component({
    selector     : 'example',
    standalone   : true,
    imports      : [CommonModule],
    templateUrl  : './example.component.html',
    encapsulation: ViewEncapsulation.None,
})
export class ExampleComponent implements OnInit
{
    private _adminService = inject(AdminService);
    stats: any;

    /**
     * Constructor
     */
    constructor()
    {
    }

    ngOnInit(): void
    {
        this._adminService.getDashboardStats().subscribe((stats) => {
            this.stats = stats;
        });
    }
}
