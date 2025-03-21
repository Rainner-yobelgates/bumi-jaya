<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemPriceRelationManager extends RelationManager
{
    protected static string $relationship = 'itemPrice';
    protected static ?string $label = 'Harga Barang';
    protected static ?string $title = 'Harga Barang';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('attr')
                ->label('Tipe (contoh: kg)')
                    ->required()
                    ->maxLength(255),
                    TextInput::make('price')
                    ->label('Harga')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->placeholder('Masukkan harga barang')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attr')
            ->columns([
                TextColumn::make('attr')
                ->label('Tipe (kg)')
                ->searchable(),
                TextColumn::make('price')
                ->label('Harga')
                ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data, RelationManager $livewire) {
                    $data['item_id'] = $livewire->ownerRecord->id;
                    return $data;
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
