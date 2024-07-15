<?php

namespace App\Filament\Resources;

use App\Enums\Region;
use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use App\Models\Conference;
use App\Models\Venue;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->aside()
                    ->description('Provide general details about the event.')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->required(),
                    ]),
                    Forms\Components\Section::make('Schedule')
                        ->aside()
                        ->description('Set the event schedule.')
                        ->icon('heroicon-o-calendar')
                        ->collapsible()
                        ->schema([
                            Forms\Components\DateTimePicker::make('start_date')
                                ->native(false)
                                ->required(),
                            Forms\Components\DateTimePicker::make('end_date')
                                ->native(false)
                                ->required(),
                        ]),
                        Forms\Components\Section::make('Location')
                            ->aside()
                            ->description('Select the event location and venue.')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Select::make('region')
                                    ->live()
                                    ->enum(Region::class)
                                    ->options(Region::class)
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    // FIXME: when editing the venue's region inline update the region field accordingly
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('venue_id', null);
                                    })
                                    ->required(),
                                Forms\Components\Select::make('venue_id')
                                    ->editOptionForm(Venue::getForm())
                                    ->createOptionForm(Venue::getForm())
                                    ->reactive()
                                    ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                        return $query->where('region', $get('region'));
                                    }),
                            ]),
                            Forms\Components\Section::make('Additional Information')
                                ->aside()
                                ->description('Set the event status and speakers.')
                                ->icon('heroicon-o-information-circle')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\TextInput::make('status')
                                        ->required(),
                                    Forms\Components\CheckboxList::make('speakers')
                                        ->columnSpanFull()
                                        ->columns(['md' => 2, 'lg' => 3])
                                        ->relationship('speakers', 'name'),
                                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('venue.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
