<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\Region;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class Venue extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'region' => Region::class,
    ];

    public function conferences(): HasMany
    {
        return $this->hasMany(Conference::class);
    }

    // NOTE: I don't like this being in the model itself.
    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required(),
            TextInput::make('city')
                ->required(),
            TextInput::make('country')
                ->required(),
            TextInput::make('postal_code')
                ->required(),
            Select::make('region')
                ->enum(Region::class)
                ->options(Region::class)
                ->searchable()
                ->preload()
                ->required(),
        ];
    }

}
