<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiUsageLogResource\Pages;
use App\Models\AiUsageLog;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AiUsageLogResource extends Resource
{
    protected static ?string $model = AiUsageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?int $navigationSort = 55;

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return Filament::getCurrentPanel()?->getId() === 'admin'
            && $user
            && $user->hasRole(Utils::getSuperAdminName());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.ai_usage.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('admin.ai_usage.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.ai_usage.plural_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-shield::filament-shield.nav.group');
    }

    /**
     * @return Builder<AiUsageLog>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user:id,name,email']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('transaction.table.date.label'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('admin.ai_usage.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('feature')
                    ->label(__('admin.ai_usage.feature'))
                    ->badge()
                    ->formatStateUsing(static function (string $state): string {
                        $key = 'admin.ai_usage.features.'.$state;
                        $t = __($key);

                        return $t !== $key ? $t : $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->label(__('admin.ai_usage.model'))
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('prompt_tokens')
                    ->label(__('admin.ai_usage.prompt_tokens'))
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('completion_tokens')
                    ->label(__('admin.ai_usage.completion_tokens'))
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_tokens')
                    ->label(__('admin.ai_usage.total_tokens'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_eur')
                    ->label(__('admin.ai_usage.cost_eur'))
                    ->formatStateUsing(function ($state): string {
                        if ($state === null) {
                            return '—';
                        }

                        return '€'.number_format((float) $state, 6);
                    }),
                Tables\Columns\TextColumn::make('estimated_cost_usd')
                    ->label(__('admin.ai_usage.estimated_cost'))
                    ->formatStateUsing(function ($state): string {
                        if ($state === null) {
                            return '—';
                        }

                        return '$'.number_format((float) $state, 6);
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('feature')
                    ->options(fn (): array => [
                        AiUsageLog::FEATURE_FINANCE_TIPS => __('admin.ai_usage.features.finance_tips'),
                        AiUsageLog::FEATURE_TRANSACTION_IMPORT => __('admin.ai_usage.features.transaction_import'),
                        AiUsageLog::FEATURE_RECEIPT_SCAN => __('admin.ai_usage.features.receipt_scan'),
                        AiUsageLog::FEATURE_PLANANCE_SUPPORT => __('admin.ai_usage.features.planance_support'),
                    ]),
            ])
            ->actions([])
            ->bulkActions([])
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiUsageLogs::route('/'),
        ];
    }
}
