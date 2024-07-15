<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakerResource\Pages;
use App\Filament\Resources\SpeakerResource\RelationManagers;
use App\Models\Speaker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpeakerResource extends Resource
{
    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\Textarea::make('bio')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('twitter_handle')
                    ->required(),
                Forms\Components\CheckboxList::make('qualifications')
                    ->hint('Select all that apply.')
                    ->helperText('These are the qualifications of the speaker.')
                    ->searchable()
                    ->bulkToggleable()
                    ->columnSpanFull()
                    ->columns(['md' => 2, 'lg' => 3])
                    ->options([
                        'community-member' => 'Community Member',
                        'youtube-creator' => 'YouTube Creator',
                        'conference-speaker' => 'Conference Speaker',
                        'conference-organizer' => 'Conference Organizer',
                        'podcast-host' => 'Podcast Host',
                    ])
                    ->descriptions([
                        'community-member' => 'A member of the community.',
                        'youtube-creator' => 'A creator on YouTube.',
                        'conference-speaker' => 'A speaker at conferences.',
                        'conference-organizer' => 'An organizer of conferences.',
                        'podcast-host' => 'A host of a podcast.',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('twitter_handle')
                    ->searchable(),
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
            'index' => Pages\ListSpeakers::route('/'),
            'create' => Pages\CreateSpeaker::route('/create'),
            'edit' => Pages\EditSpeaker::route('/{record}/edit'),
        ];
    }
}
