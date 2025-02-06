<?php

namespace App\Filament\Admin\Resources\TraineeResource\RelationManagers;

use App\Models\TraineesVerifiedRequest;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TraineesVerificationRequestRelationManager extends RelationManager
{
    protected static string $relationship = 'verified_requests';

    protected static ?string $title = 'Verification requests';

    public static function getBadge(Model $owner_record, string $page_class): ?string
    {
        return $owner_record->verified_requests->count();
    }

    public static function getBadgeColor(Model $owner_record, string $page_class): ?string
    {
        return static::getBadge($owner_record, $page_class) > 0 ? 'warning' : 'primary';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('This trainee has not made any verification requests yet.')
            ->columns([
                TextColumn::make('trainee.activeGroup.group.group_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Group'),
                TextColumn::make('staff_user_name')
                    ->label('Approved by staff'),
                IconColumn::make('is_checked_by_staff')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success'
                    }),
                IconColumn::make('is_verified')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-o-check-circle'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success'
                    }),
                TextColumn::make('requested_at')
                    ->sortable()
                    ->dateTime('M d, Y h:i:s A'),
                TextColumn::make('verified_at')
                    ->sortable()
                    ->dateTime('M d, Y h:i:s A')
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->groups([
                //
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Verify trainee info request')
                    ->requiresConfirmation()
                    ->modalHeading('Verify trainee info request?')
                    ->modalDescription('Are you sure you would like to verify this request? The trainee will not be able to make any changes once verified.')
                    ->hidden(fn (TraineesVerifiedRequest $trainees_verified_request): bool => $trainees_verified_request->is_checked_by_staff == 1)
                    ->action(function (TraineesVerifiedRequest $record) {
                        $record->staff_user_id = Auth::user()->id;
                        $record->is_checked_by_staff = 1;
                        $record->is_verified = 1;

                        $record->save();

                        // TODO: TO ADD A NOTIFICATION?
                }),
                Action::make('unverify')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip('Unverify trainee info request')
                    ->requiresConfirmation()
                    ->modalHeading('Unverify trainee info request?')
                    ->modalDescription('Are you sure you would like to unverify this request? The trainee will have to submit another request.')
                    ->hidden(fn (TraineesVerifiedRequest $trainees_verified_request): bool => $trainees_verified_request->is_checked_by_staff == 1)
                    ->action(function (TraineesVerifiedRequest $record) {
                        $record->staff_user_id = Auth::user()->id;
                        $record->is_checked_by_staff = 1;
                        $record->is_verified = 0;

                        $record->save();

                        // TODO: TO ADD A NOTIFICATION?
                }),
            ])
            ->bulkActions([
                //
            ]);
    }
}
