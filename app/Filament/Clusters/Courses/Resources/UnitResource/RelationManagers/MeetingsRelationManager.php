<?php

namespace App\Filament\Clusters\Courses\Resources\UnitResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Meetings\MeetingType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MeetingsRelationManager extends RelationManager
{
    protected static string $relationship = 'meetings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('meeting_type_id')
                    ->options(MeetingType::all()->pluck('name', 'id'))
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set, ?string $state) {                    
                        $meetingTypeName = MeetingType::find($state)?->name;
                        if (Str::contains($meetingTypeName, 'support')) {
                            $set('description', ($meetingTypeName ? $meetingTypeName . ' about theme ' : ''));
                        } else {
                            $set('description', ($meetingTypeName ? $meetingTypeName . ' about unit ' : ''));
                        }
                    })
                    ->required(),
                TextInput::make('description')
                    ->label('Name of the meeting')
                    ->helperText('This should be formatted as "1:1 meeting about unit X" or "group meeting about unit X",')
                    ->required()
                    ->maxLength(255),
                Textarea::make('internal_notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add a new meeting to this unit'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
