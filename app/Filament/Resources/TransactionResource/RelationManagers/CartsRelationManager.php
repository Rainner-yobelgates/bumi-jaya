<?php

namespace App\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CartsRelationManager extends RelationManager
{
    protected static string $relationship = 'carts';
    protected static ?string $label = 'Daftar Barang';
    protected static ?string $title = 'Daftar Barang';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                ->label('Nama Barang')
                ->searchable(),
                TextColumn::make('merk')
                ->label('Merek'),
                TextColumn::make('attr')
                ->label('Tipe (Kg)'),
                TextColumn::make('price')
                ->label('Harga')
                ->money('IDR'),
                TextColumn::make('quantity')
                ->label('Jumlah'),
                TextColumn::make('total_price')
                ->label('Total Harga')
                ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
