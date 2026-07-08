<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;
    
    protected static ?string $navigationLabel = 'Inicio';
    
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.dashboard';
}