<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use App\Models\Cart;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCarts extends ListRecords
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    TextInput::make('name')
                        ->label('Nama')
                        ->required(),
                    TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->required(),
                    Textarea::make('address')
                        ->label('Alamat')

                ])
                ->action(function (array $data) {
                    $totalPrice = Cart::whereNull('transaction_id')->sum('total_price');

                    $transaction = Transaction::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'total_price' => $totalPrice,
                        'address' => $data['address'],
                    ]);

                    Cart::whereNull('transaction_id')->update([
                        'transaction_id' => $transaction->id,
                    ]);

                    Notification::make()
                        ->title('Transaksi Berhasil')
                        ->success()
                        ->body('Semua item dalam keranjang telah disimpan dalam transaksi.')
                        ->send();
                }),
        ];
    }
}
