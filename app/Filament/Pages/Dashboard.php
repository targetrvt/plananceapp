<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = null;
    protected static ?string $title = null;
    
    public static function getNavigationLabel(): string
    {
        return __('dashboard.navigation.label');
    }
    
    public function getTitle(): string
    {
        return __('dashboard.title');
    }
}