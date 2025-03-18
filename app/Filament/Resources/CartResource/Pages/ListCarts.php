<?php

namespace App\Filament\Resources\CartResource\Pages;

use App\Filament\Resources\CartResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCarts extends ListRecords
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear_cart')
            ->label('Bersihkan')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation() // Konfirmasi sebelum menghapus
            ->action(function () {
                \App\Models\Cart::truncate(); // Menghapus semua data di tabel Cart

                Notification::make()
                    ->title('Keranjang Dikosongkan')
                    ->success()
                    ->body('Semua item telah dihapus dari keranjang.')
                    ->send();
            }),
            Action::make('print_pdf')
            ->label('Print PDF')
            ->icon('heroicon-o-printer')
            ->color('primary')
            ->url(fn () => route('generate.pdf')) // Arahkan ke rute yang akan menangani PDF
            ->openUrlInNewTab(), // Membuka di tab baru agar tidak mengganti halaman
        ];
    }
}
