<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Filament\Resources\ItemResource\RelationManagers\ItemPriceRelationManager;
use App\Models\Item;
use App\Models\ItemPrice;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Barang')
                    ->placeholder('Masukkan nama barang')
                    ->required(),
                TextInput::make('merk')
                ->label('Merek')
                ->placeholder('Masukkan merk')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Nama Barang')
                ->searchable(),
                TextColumn::make('merk')
                ->label('Merek'),
                TextColumn::make('itemPrice')
                ->label('Harga Barang')
                ->getStateUsing(fn ($record) => 
                    $record->itemPrice?->map(fn ($price) => "{$price->attr}: Rp" . number_format($price->price, 0, ',', '.'))
                    ->join(', ') ?? 'No prices available'
                )
            ])
            ->filters([
                //
            ])
            ->actions([
            Tables\Actions\EditAction::make(),
            Action::make('add_to_cart')
                ->label('Keranjang')
                ->icon('heroicon-o-shopping-cart')
                ->modalHeading('Pilih Tipe & Jumlah')
                ->form([
                    Select::make('attribute')
                        ->label('Pilih Tipe')
                        ->options(fn ($record) => 
                            $record->itemPrice->pluck('attr', 'id')
                        )
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $price = \App\Models\ItemPrice::find($state)?->price ?? 0;
                            $formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');
                            
                            $set('price', $formattedPrice);
                            $set('total_price', 'Rp ' . number_format($price * ($get('quantity') ?? 1), 0, ',', '.'));
                        }),
                        TextInput::make('quantity')
                        ->label('Jumlah')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->reactive() 
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $price = intval(str_replace(['Rp ', '.'], '', $get('price'))); 
                            $total = $price * ($state ?? 1);
                            
                            $set('total_price', 'Rp ' . number_format($total, 0, ',', '.'));
                        }),
                    TextInput::make('price')
                        ->label('Harga')
                        ->disabled()
                        ->dehydrated(true),
                    TextInput::make('total_price')
                        ->label('Total Harga')
                        ->disabled()
                        ->dehydrated(true),
                ])
                ->action(function (array $data, $record) {
                    $attributeName = \App\Models\ItemPrice::find($data['attribute'])?->attr ?? 'Unknown';
                    \App\Models\Cart::create([
                        'name' => $record->name,
                        'merk' => $record->merk,
                        'attr' => $attributeName,
                        'quantity' => $data['quantity'],
                        'price' => intval(str_replace(['Rp ', '.'], '', $data['price'])),
                        'total_price' => intval(str_replace(['Rp ', '.'], '', $data['total_price'])), // Bersihkan format harga sebelum disimpan
                    ]);

                    Notification::make()
                        ->title('Item Ditambahkan')
                        ->success()
                        ->body('Item telah berhasil ditambahkan ke keranjang.')
                        ->send();
                }),
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
            ItemPriceRelationManager::class, 
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
