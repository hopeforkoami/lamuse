import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { AdminService } from 'app/modules/admin/admin.service';

@Component({
    selector: 'app-pricing',
    standalone: true,
    imports: [CommonModule, FormsModule, MatTableModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatInputModule],
    templateUrl: './pricing.component.html'
})
export class PricingComponent implements OnInit {
    private _adminService = inject(AdminService);
    pricingRules: any[] = [];
    displayedColumns: string[] = ['star_level', 'currency_code', 'min_price', 'max_price', 'actions'];

    ngOnInit(): void {
        this.loadPricingRules();
    }

    loadPricingRules(): void {
        this._adminService.getPricingRules().subscribe(data => {
            this.pricingRules = data;
        });
    }

    saveRule(rule: any): void {
        this._adminService.upsertPricingRule(rule).subscribe(() => {
            this.loadPricingRules();
        });
    }

    addRule(): void {
        this.pricingRules = [
            ...this.pricingRules,
            { star_level: 1, currency_code: 'XOF', min_price: 0, max_price: 0 }
        ];
    }
}
