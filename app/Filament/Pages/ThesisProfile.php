<?php

namespace App\Filament\Pages;

use App\Models\ThesisProfile as ThesisProfileModel;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ThesisProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?string $title = 'Profil Skripsi';
    
    protected string $view = 'filament.pages.thesis-profile';

    public ?array $data = [];

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $profile = $user->thesisProfile()->firstOrCreate();
        $this->form->fill($profile->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Utama')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Skripsi')
                            ->columnSpanFull()
                            ->maxLength(500),
                        TextInput::make('study_program')
                            ->label('Program Studi')
                            ->maxLength(255),
                        TextInput::make('faculty')
                            ->label('Fakultas')
                            ->maxLength(255),
                    ])->columns(2),
                Section::make('Dosen Pembimbing')
                    ->schema([
                        TextInput::make('supervisor_name')
                            ->label('Dosen Pembimbing 1')
                            ->maxLength(255),
                        TextInput::make('co_supervisor_name')
                            ->label('Dosen Pembimbing 2')
                            ->maxLength(255),
                    ])->columns(2),
                Section::make('Target Waktu')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai Skripsi'),
                        DatePicker::make('target_completion')
                            ->label('Target Penyelesaian'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $profile = $user->thesisProfile()->firstOrCreate();
        $profile->update($this->form->getState());

        Notification::make()
            ->title('Profil skripsi berhasil disimpan')
            ->success()
            ->send();
    }
}
