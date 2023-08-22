<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\HtmlString;

class Quickview extends BaseWidget
{
    

    protected function getCards(): array
    {
        return [];
        return [
            Card::make(new HtmlString('
            <style>
            .badge-red {
                background-color: red;
                color: white;
                padding: 4px 6px;
                text-align: center;
                border-radius: 5px;
            }
            .badge-blue {
                background-color: blue;
                color: white;
                padding: 4px 6px;
                text-align: center;
                border-radius: 5px;
            }
            .badge-green {
                background-color: green;
                color: white;
                padding: 4px 6px;
                text-align: center;
                border-radius: 5px;
            }
            .badge-orange {
                background-color: orange;
                color: white;
                padding: 4px 6px;
                text-align: center;
                border-radius: 5px;
            }
            table {
                border-collapse: collapse;
            }
           
            th, td {
                padding:1px
            }
            </style>
            <span style="color:white">LEAVE CREDITS<span>'),
            new HtmlString('
            <table>
            <tr>
            <td><span class="badge-blue" style="font-size:15px !important;">20</span>&nbsp;</td>
            <td><span style="font-size:15px !important;">Vacation</span></td>
            </tr>
            <tr>
            <td><span class="badge-red" style="font-size:15px !important;">20</span></td>
            <td><span style="font-size:15px !important;">Sick</span></td>
            </tr>

            <tr>
            <td> <span class="badge-green" style="font-size:15px !important;">20</span></td>
            <td><span style="font-size:15px !important;">Emergency</span></td>
            </tr>
            <tr>
            <td><span class="badge-orange" style="font-size:15px !important;">0</span></td>
            <td><span style="font-size:15px !important;">Birthday</span></td>
            </tr>
            </table>
            ')),
            Card::make('Unique views', '192.1k')
            ->description('32k increase')
            ->descriptionIcon('heroicon-s-trending-up')
            ->chart([18000, 20030, 102, 3, 1115, 4, 17])
            ->color('success'),
            Card::make('Hollidays', '3:12'),
        ];
    }
}
