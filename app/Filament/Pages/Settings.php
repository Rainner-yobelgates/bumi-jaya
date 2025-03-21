<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class Settings extends Page implements HasForms

{
    use InteractsWithForms;

    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $label = 'Pengaturan';
    protected static ?string $title = 'Pengaturan';
    protected static string $view = 'filament.pages.settings';
    protected static ?int $navigationSort = 5;

    public function mount(): void
    {
        $this->form->fill(Setting::first()?->toArray());
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('logo')
                    ->image()
                    ->directory('logo')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ]),
                Textarea::make('alamat')
                    ->placeholder('Masukkan alamat')
                    ->label('Alamat Toko')
                    ->rows(5)
                    ->columnSpan(2),
                TextInput::make('no_telp')
                    ->mask('9999-9999-9999')
                    ->rule('regex:/^[0-9\-]+$/')
                    ->placeholder('Masukkan nomor telepon')
                    ->label('No Telepon'),
            ])
            ->statePath('data');
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            Setting::updateOrCreate(
                ['id' => 1],
                $data
            );
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
}
