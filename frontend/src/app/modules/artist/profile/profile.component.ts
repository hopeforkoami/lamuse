import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ArtistService } from 'app/modules/artist/artist.service';
import { UserService } from 'app/core/user/user.service';
import { User } from 'app/core/user/user.types';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSnackBarModule, MatSnackBar } from '@angular/material/snack-bar';

@Component({
    selector     : 'artist-profile',
    standalone   : true,
    templateUrl  : './profile.component.html',
    imports      : [
        CommonModule, MatIconModule, MatButtonModule, FormsModule,
        ReactiveFormsModule, MatFormFieldModule, MatInputModule, MatSnackBarModule
    ],
    encapsulation: ViewEncapsulation.None
})
export class ProfileComponent implements OnInit
{
    profileForm: FormGroup;
    user: User;

    constructor(
        private _artistService: ArtistService,
        private _userService: UserService,
        private _formBuilder: FormBuilder,
        private _snackBar: MatSnackBar
    ) {
        this.profileForm = this._formBuilder.group({
            name: ['', Validators.required],
            country: ['', Validators.required],
            email: [{value: '', disabled: true}]
        });
    }

    ngOnInit(): void
    {
        this._userService.user$.subscribe((user: User) => {
            this.user = user;
            this.profileForm.patchValue({
                name: user.name,
                country: user.country,
                email: user.email
            });
        });
    }

    onSubmit(): void
    {
        if (this.profileForm.invalid) return;

        this._artistService.updateProfile(this.profileForm.getRawValue()).subscribe((updatedUser) => {
            this._userService.setUser(updatedUser);
            this._snackBar.open('Profile updated successfully', 'OK', { duration: 3000 });
        });
    }
}
