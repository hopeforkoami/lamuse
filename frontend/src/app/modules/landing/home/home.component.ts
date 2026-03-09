import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { Router, RouterLink } from '@angular/router';
import { StoreService } from 'app/core/store/store.service';
import { CartService } from 'app/core/cart/cart.service';

@Component({
    selector     : 'landing-home',
    templateUrl  : './home.component.html',
    encapsulation: ViewEncapsulation.None,
    standalone   : true,
    imports      : [CommonModule, MatButtonModule, RouterLink, MatIconModule, MatCardModule],
})
export class LandingHomeComponent implements OnInit
{
    songs: any[] = [];

    /**
     * Constructor
     */
    constructor(
        private _storeService: StoreService,
        private _cartService: CartService,
        private _router: Router
    )
    {
    }

    ngOnInit(): void {
        this._storeService.getSongs().subscribe((data) => {
            this.songs = data;
        });
    }

    addToCart(song: any): void {
        this._cartService.addToCart(song);
        this._router.navigateByUrl('/checkout');
    }
}
