<?php

namespace App\Filament\Resources\MilestoneResource\RelationManagers;

use App\Models\MilestoneDocument;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Nama Berkas')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Toggle::make('is_completed')
                    ->label('Sudah Lengkap')
                    ->default(false),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Berkas')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Sudah Lengkap')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Nama File')
                    ->placeholder('Belum di-upload')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->formatStateUsing(fn ($state) => $state ? \Illuminate\Support\Number::fileSize($state) : '-')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\Action::make('toggleCompleted')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (MilestoneDocument $record) {
                        $record->update([
                            'is_completed' => !$record->is_completed,
                        ]);
                    }),
                Actions\Action::make('uploadFile')
                    ->label('Upload Berkas')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('primary')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Pilih Berkas')
                            ->required()
                            ->disk('local')
                            ->directory('private/documents')
                            ->visibility('private')
                            ->maxSize(10240)
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'image/jpeg',
                                'image/png'
                            ])
                            ->getUploadedFileNameForStorageUsing(
                                fn ($file): string => Str::uuid() . '.' . $file->getClientOriginalExtension()
                            ),
                    ])
                    ->action(function (MilestoneDocument $record, array $data) {
                        if ($record->file_path) {
                            Storage::disk('local')->delete($record->file_path);
                        }

                        $filePath = $data['file'];
                        $fileSize = Storage::disk('local')->size($filePath);

                        $record->update([
                            'file_path' => $filePath,
                            'file_name' => basename($filePath),
                            'file_size' => $fileSize,
                            'is_completed' => true,
                        ]);
                    }),
                Actions\Action::make('downloadFile')
                    ->label('Unduh')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn (MilestoneDocument $record) => !empty($record->file_path))
                    ->action(function (MilestoneDocument $record) {
                        return Storage::disk('local')->download($record->file_path, $record->file_name);
                    }),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
