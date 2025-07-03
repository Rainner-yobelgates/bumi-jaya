<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use App\Models\Cart;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\RawJs;

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
                        ->label('Alamat'),
                    TextInput::make('total_price')
                        ->label('Total Harga')
                        ->default(function () {
                            return \App\Models\Cart::where('transaction_id', null)
                            ->get()
                            ->sum(fn($cart) => $cart->total_price);
                        })
                        ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('discount')
                        ->label('Diskon')
                        ->numeric()
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->debounce(500)
                        ->dehydrated(true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $total = \App\Models\Cart::where('transaction_id', null)
                                ->get()
                                ->sum(fn($cart) => $cart->total_price);

                            $discount = (int) str_replace(',', '', $state);
                            $final = max($total - $discount, 0);

                            $set('total', $final);
                            $set('total_formatted', 'Rp ' . number_format($final, 0, ',', '.'));
                        }),
                    Placeholder::make('total_formatted')
                    ->label('Harga Setelah Diskon')
                    ->reactive()
                    ->content(function ($get) {
                        $formatted = $get('total_formatted');

                        if ($formatted) {
                            return $formatted;
                        }

                        $total = \App\Models\Cart::where('transaction_id', null)
                            ->get()
                            ->sum(fn($cart) => $cart->total_price);

                        return 'Rp ' . number_format($total, 0, ',', '.');
                    }),

                    Hidden::make('total')
                        ->dehydrated(true)
                        ->default(function () {
                            return \App\Models\Cart::where('transaction_id', null)
                                ->get()
                                ->sum(fn($cart) => $cart->total_price);
                        }),
                        
                ])
                ->action(function (array $data) {
                    $totalPrice = Cart::whereNull('transaction_id')->sum('total_price');

                    $transaction = Transaction::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'total_price' => $totalPrice,
                        'address' => $data['address'],
                        'discount' => $data['discount'] ?? 0,
                        'total' => $data['total'] ?? 0,
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
