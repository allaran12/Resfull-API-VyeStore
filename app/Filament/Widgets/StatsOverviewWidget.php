<?php
 
 namespace App\Filament\Widgets;

use App\Models\Games;
use App\Models\Penjualan;
 use App\Models\User;
 use Filament\Widgets\StatsOverviewWidget as BaseWidget;
 use Filament\Widgets\StatsOverviewWidget\Card;
  
 class StatsOverviewWidget extends BaseWidget
 {
     protected function getCards(): array
     {
         return [
             // Card::make('Total Users', User::count())
             //  ->description('user yang terdaftar')
             //  ->chart([7,3,4,5,6,3,5,3,]),
             // Card::make('Bounce rate', '21%'),
             // Card::make('Average time on page', '3:12'),
 
            //  Card::make('Admin', User::where('role', 'admin')->count())
            //  ->description('admin yang aktif')
            //  ->color('blue')
            //  ->chart([2, 3, 4, 5, 1, 9]),
 
             Card::make('Total user', User::where('role', 'users')->count())
             ->description('user yang telah register')
             ->chart([2, 3, 4, 5, 1, 3, 4, 3]),
 
             Card::make('Total Transaksi', Penjualan::count())
             ->description('semua penjualan')
             ->chart([3, 1, 8, 1, 3, 3, 7, 5]),

             Card::make('Total Games    ', Games::count())
             ->description('semua game')
             ->chart([3, 1, 8, 1, 3, 3, 7, 5]),
         ];
     }
 }
 
 