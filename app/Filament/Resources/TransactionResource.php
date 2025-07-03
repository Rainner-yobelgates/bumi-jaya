<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Filament\Resources\TransactionResource\RelationManagers\CartsRelationManager;
use App\Models\Transaction;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $label = 'Transaksi';
    protected static ?string $navigationLabel = 'Transaksi'; 
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Pelanggan'),
                TextInput::make('phone')
                    ->label('Nomor Pelanggan'),
                Textarea::make('address')
                    ->label('Alamat Pelanggan'),
                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->disabled()
                    ->numeric(),
                TextInput::make('discount')
                    ->label('Diskon')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->disabled()
                    ->numeric(),
                TextInput::make('total')
                    ->label('Harga Setelah Diskon')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->disabled()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Nama Pelanggan')
                ->searchable(),
                TextColumn::make('phone')
                ->label('Nomor Pelanggan'),
                TextColumn::make('address')
                ->label('Alamat Pelanggan'),
                TextColumn::make('total_price')
                ->label('Total Harga')
                ->money('IDR'),
                TextColumn::make('discount')
                ->label('Diskon')
                ->money('IDR'),
                TextColumn::make('total')
                ->label('Harga Setelah Diskon')
                ->money('IDR'),
                TextColumn::make('created_at')
                ->label('Dibuat')
                ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('print')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->url(fn ($record) => route('generate.pdf', ['id' => $record->id]))
                ->openUrlInNewTab(), // Agar terbuka di tab baru
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CartsRelationManager::class, 
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
