import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class CartService {
    private _cartItems: BehaviorSubject<any[]> = new BehaviorSubject<any[]>([]);
    public cartItems$: Observable<any[]> = this._cartItems.asObservable();

    constructor() {
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            this._cartItems.next(JSON.parse(savedCart));
        }
    }

    addToCart(song: any): void {
        const currentItems = this._cartItems.getValue();
        if (!currentItems.find(item => item.id === song.id)) {
            const newItems = [...currentItems, song];
            this._cartItems.next(newItems);
            localStorage.setItem('cart', JSON.stringify(newItems));
        }
    }

    removeFromCart(songId: number): void {
        const currentItems = this._cartItems.getValue();
        const newItems = currentItems.filter(item => item.id !== songId);
        this._cartItems.next(newItems);
        localStorage.setItem('cart', JSON.stringify(newItems));
    }

    clearCart(): void {
        this._cartItems.next([]);
        localStorage.removeItem('cart');
    }

    getCartTotal(): number {
        return this._cartItems.getValue().reduce((total, item) => total + parseFloat(item.price), 0);
    }
}
