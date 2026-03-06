import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { AdminService } from 'app/modules/admin/admin.service';

@Component({
    selector: 'app-payment-health',
    standalone: true,
    imports: [CommonModule, MatCardModule, MatIconModule, MatButtonModule],
    templateUrl: './payment-health.component.html'
})
export class PaymentHealthComponent implements OnInit {
    private _adminService = inject(AdminService);
    healthStatus: any[] = [];

    ngOnInit(): void {
        this.loadHealthStatus();
    }

    loadHealthStatus(): void {
        this._adminService.getPaymentHealth().subscribe(data => {
            this.healthStatus = data;
        });
    }

    refreshHealth(): void {
        // Ideally there should be a refresh endpoint that triggers checks
        this.loadHealthStatus();
    }
}
