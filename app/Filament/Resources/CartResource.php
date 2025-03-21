<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Filament\Resources\CartResource\RelationManagers;
use App\Models\Cart;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\View;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;
    protected static ?string $label = 'Keranjang';
    protected static ?string $navigationLabel = 'Keranjang'; 
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Barang')
                    ->disabled(),
                TextInput::make('merk')
                    ->label('Merek')
                    ->disabled(),
                TextInput::make('attr')
                    ->label('Tipe')
                    ->disabled(),
                TextInput::make('price')
                    ->label('Harga')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->disabled()
                    ->numeric(),
                    TextInput::make('quantity')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->reactive() 
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $price = (float) str_replace(',', '', $get('price')); 
                        $quantity = (int) ($state ?? 1); 
                        $total = $price * $quantity;
        
                        $set('total_price', $total); 
                    }),
                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->disabled()
                    ->dehydrated(true)
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(\App\Models\Cart::whereNull('transaction_id'))
            ->columns([
                TextColumn::make('name')
                ->label('Nama Barang')
                ->searchable(),
                TextColumn::make('merk')
                ->label('Merek'),
                TextColumn::make('attr')
                ->label('Tipe'),
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->contentFooter(fn () => view('components.table.extra_row', [
                'total' => \App\Models\Cart::whereNull('transaction_id')->sum('total_price')
            ]))->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }
}
