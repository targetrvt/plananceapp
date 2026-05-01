<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exceptions\AiAccessDeniedException;
use App\Filament\Resources\TransactionResource;
use App\Services\Finance\TransactionImportService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('transaction.actions.create.label')),
            Actions\Action::make('import_transactions')
                ->label(__('transaction.import.action_label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->modalHeading(__('transaction.import.modal_heading'))
                ->modalDescription(__('transaction.import.modal_description'))
                ->visible(fn (): bool => Filament::getCurrentPanel()?->getId() === 'premium'
                    && (bool) auth()->user()?->hasPremiumSubscription())
                ->form([
                    Forms\Components\FileUpload::make('import_file')
                        ->label(__('transaction.import.file_label'))
                        ->helperText(__('transaction.import.helper'))
                        ->storeFiles(false)
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/pdf',
                        ])
                        ->maxSize(16384)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $upload = $data['import_file'] ?? null;
                    $path = is_object($upload) && method_exists($upload, 'getRealPath')
                        ? $upload->getRealPath()
                        : null;
                    $name = is_object($upload) && method_exists($upload, 'getClientOriginalName')
                        ? $upload->getClientOriginalName()
                        : basename((string) $path);

                    if (! $path || ! is_readable($path)) {
                        Notification::make()
                            ->title(__('transaction.import.messages.failed_title'))
                            ->body(__('transaction.import.messages.unreadable'))
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $result = app(TransactionImportService::class)->importFromTemporaryPath(
                            $path,
                            $name,
                            (int) auth()->id()
                        );

                        $lines = [
                            __('transaction.import.messages.summary', [
                                'imported' => $result->imported,
                                'skipped' => $result->skipped,
                            ]),
                        ];

                        if ($result->errors !== []) {
                            $lines[] = implode("\n", array_slice($result->errors, 0, 8));
                            if (count($result->errors) > 8) {
                                $lines[] = '…';
                            }
                        }

                        Notification::make()
                            ->title(__('transaction.import.messages.success_title'))
                            ->body(implode("\n\n", $lines))
                            ->success()
                            ->send();
                    } catch (AiAccessDeniedException $e) {
                        Notification::make()
                            ->title(__('messages.ai_access.denied_title'))
                            ->body($e->getMessage())
                            ->warning()
                            ->send();
                    } catch (\Throwable $e) {
                        report($e);
                        Notification::make()
                            ->title(__('transaction.import.messages.failed_title'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
