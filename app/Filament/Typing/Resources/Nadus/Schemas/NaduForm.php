<?php

namespace App\Filament\Typing\Resources\Nadus\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NaduForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('නඩු')->schema([
                TextInput::make('nadu_ankaya')->label('නඩු අංකය')->required(),
                DatePicker::make('dun_dinaya')->label('දුන් දිනය'),
                TextInput::make('kalaya')->label('කාලය'),
            ])->columns(3)->columnSpanFull(),
            Section::make('ණයකරු 1')->schema(self::personFields('nayakaru1_'))->columns(2)->columnSpanFull(),
            Section::make('ණයකරු 2')->schema(self::personFields('nayakaru2_'))->columns(2)->columnSpanFull(),
            Section::make('ඇපකරු 1')->schema(self::personFields('aepakaru1_'))->columns(2)->columnSpanFull(),
            Section::make('ඇපකරු 2')->schema(self::personFields('aepakaru2_'))->columns(2)->columnSpanFull(),
            Section::make('මූල්‍ය විස්තර')->schema([
                TextInput::make('dun_naya_mudala')->label('දුන් ණය මුදල')->numeric(),
                TextInput::make('arawul_mudala')->label('ආරවුල් මුදල')->numeric(),
                TextInput::make('poli_prathishathaya')->label('පොලී ප්‍රතිශතය')->numeric(),
                TextInput::make('awasan_mudal_bendima')->label('අවසන් මුදල් බැඳීම')->numeric(),
                TextInput::make('dina_ganuna')->label('දින ගණන')->numeric(),
                TextInput::make('mul_mudala')->label('මුල් මුදල')->numeric(),
                TextInput::make('poliya')->label('පොලිය')->numeric(),
                TextInput::make('nadu_gasthu')->label('නඩු ගාස්තු')->numeric(),
                TextInput::make('total')->label('එකතුව')->numeric(),
            ])->columns(3)->columnSpanFull(),
        ]);
    }

    private static function personFields(string $prefix): array
    {
        return [
            TextInput::make($prefix.'nama')->label('නම'),
            TextInput::make($prefix.'samajika_ankaya')->label('සාමාජික අංකය'),
            TextInput::make($prefix.'lipinaya1')->label('ලිපිනය 1'),
            TextInput::make($prefix.'lipinaya2')->label('ලිපිනය 2'),
            TextInput::make($prefix.'lipinaya3')->label('ලිපිනය 3'),
        ];
    }
}
