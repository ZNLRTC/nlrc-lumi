<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Mail\ManuallyCreatedNewUserPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $email = $this->record->email;
        $password = $this->data['password'];

        if ($this->data['send_email']) {
            Mail::to($email)->send(new ManuallyCreatedNewUserPasswordMail($email, $password));
        }
    }
}
