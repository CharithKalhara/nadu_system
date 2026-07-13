<?php

namespace App\Filament\Resources\Nadus\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NaduInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('නඩු')
                    ->schema([
                        TextEntry::make('nadu_ankaya')->label('නඩු අංකය'),
                        TextEntry::make('dun_dinaya')->label('දුන් දිනය')->date(),
                        TextEntry::make('kalaya')->label('කාලය'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('ණයකරු 1')
                    ->schema([
                        TextEntry::make('nayakaru1_nama')->label('නම'),
                        TextEntry::make('nayakaru1_samajika_ankaya')->label('සාමාජික අංකය'),
                        TextEntry::make('nayakaru1_lipinaya1')->label('ලිපිනය 1'),
                        TextEntry::make('nayakaru1_lipinaya2')->label('ලිපිනය 2'),
                        TextEntry::make('nayakaru1_lipinaya3')->label('ලිපිනය 3'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ණයකරු 2')
                    ->schema([
                        TextEntry::make('nayakaru2_nama')->label('නම'),
                        TextEntry::make('nayakaru2_samajika_ankaya')->label('සාමාජික අංකය'),
                        TextEntry::make('nayakaru2_lipinaya1')->label('ලිපිනය 1'),
                        TextEntry::make('nayakaru2_lipinaya2')->label('ලිපිනය 2'),
                        TextEntry::make('nayakaru2_lipinaya3')->label('ලිපිනය 3'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ඇපකරු 1')
                    ->schema([
                        TextEntry::make('aepakaru1_nama')->label('නම'),
                        TextEntry::make('aepakaru1_samajika_ankaya')->label('සාමාජික අංකය'),
                        TextEntry::make('aepakaru1_lipinaya1')->label('ලිපිනය 1'),
                        TextEntry::make('aepakaru1_lipinaya2')->label('ලිපිනය 2'),
                        TextEntry::make('aepakaru1_lipinaya3')->label('ලිපිනය 3'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ඇපකරු 2')
                    ->schema([
                        TextEntry::make('aepakaru2_nama')->label('නම'),
                        TextEntry::make('aepakaru2_samajika_ankaya')->label('සාමාජික අංකය'),
                        TextEntry::make('aepakaru2_lipinaya1')->label('ලිපිනය 1'),
                        TextEntry::make('aepakaru2_lipinaya2')->label('ලිපිනය 2'),
                        TextEntry::make('aepakaru2_lipinaya3')->label('ලිපිනය 3'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('මූල්‍ය විස්තර')
                    ->schema([
                        TextEntry::make('arawul_mudala')->label('ආරවුල් මුදල')->money('LKR'),
                        TextEntry::make('dun_naya_mudala')->label('දුන් ණය මුදල')->money('LKR'),
                        TextEntry::make('poli_prathishathaya')->label('පොලී ප්‍රතිශතය'),
                        TextEntry::make('awasan_mudal_bendima')->label('අවසන් මුදල් බැඳීම')->money('LKR'),
                        TextEntry::make('dina_ganuna')->label('දින ගණන'),
                        TextEntry::make('mul_mudala')->label('මුල් මුදල')->money('LKR'),
                        TextEntry::make('poliya')->label('පොලිය')->money('LKR'),
                        TextEntry::make('nadu_gasthu')->label('නඩු ගාස්තු')->money('LKR'),
                        TextEntry::make('total')->label('එකතුව')->money('LKR'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}