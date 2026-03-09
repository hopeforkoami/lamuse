import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatRadioModule } from '@angular/material/radio';
import { StoreService } from 'app/core/store/store.service';
import { CartService } from 'app/core/cart/cart.service';
import { UserService } from 'app/core/user/user.service';
import { Router } from '@angular/router';

@Component({
    selector     : 'landing-checkout',
    templateUrl  : './checkout.component.html',
    encapsulation: ViewEncapsulation.None,
    standalone   : true,
    imports      : [CommonModule, FormsModule, ReactiveFormsModule, MatButtonModule, MatFormFieldModule, MatIconModule, MatInputModule, MatRadioModule],
})
export class LandingCheckoutComponent implements OnInit
{
    checkoutForm: UntypedFormGroup;
    cartItems: any[] = [];
    total: number = 0;
    isLoggedIn: boolean = false;

    constructor(
        private _cartService: CartService,
        private _storeService: StoreService,
        private _userService: UserService,
        private _formBuilder: UntypedFormBuilder,
        private _router: Router
    )
    {
    }

    ngOnInit(): void {
        this._cartService.cartItems$.subscribe(items => {
            this.cartItems = items;
            this.total = this._cartService.getCartTotal();
        });

        this._userService.user$.subscribe(user => {
            this.isLoggedIn = !!user;
        });

        this.checkoutForm = this._formBuilder.group({
            email: ['', [Validators.required, Validators.email]],
            payment_provider: ['paypal', Validators.required]
        });

        if (this.isLoggedIn) {
            this.checkoutForm.get('email').clearValidators();
            this.checkoutForm.get('email').updateValueAndValidity();
        }
    }

    processCheckout(): void {
        if (this.checkoutForm.invalid && !this.isLoggedIn) return;

        const orderData = {
            song_ids: this.cartItems.map(item => item.id),
            email: this.isLoggedIn ? null : this.checkoutForm.get('email').value,
            payment_provider: this.checkoutForm.get('payment_provider').value
        };

        this._storeService.checkout(orderData).subscribe(response => {
            // In a real app, redirect to payment_url
            console.log('Order created:', response);
            alert('Commande créée ! Redirection vers le paiement (Mock)...');
            this._cartService.clearCart();
            this._router.navigateByUrl('/home');
        });
    }

    removeFromCart(songId: number): void {
        this._cartService.removeFromCart(songId);
    }
}
