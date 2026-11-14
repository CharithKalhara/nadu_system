<?php

namespace App\Support;

use App\Models\Nadu;

class MobileCaseFieldCatalog
{
    /** @return list<string> */
    public static function names(): array
    {
        return array_column(array_merge(...array_column(self::sections(), 'fields')), 'key');
    }

    /** @return array<string, mixed> */
    public static function serialize(Nadu $case): array
    {
        return $case->only(array_merge(
            ['id', 'company_id'],
            self::names(),
            ['created_at', 'updated_at'],
        ));
    }

    /** @return list<array{title: string, fields: list<array{key: string, label: string, type: string, required: bool}>}> */
    public static function sections(): array
    {
        $text = static fn (string $key, string $label, bool $required = false): array => [
            'key' => $key,
            'label' => $label,
            'type' => 'text',
            'required' => $required,
        ];
        $number = static fn (string $key, string $label, string $type = 'decimal'): array => [
            'key' => $key,
            'label' => $label,
            'type' => $type,
            'required' => false,
        ];
        $date = static fn (string $key, string $label): array => [
            'key' => $key,
            'label' => $label,
            'type' => 'date',
            'required' => false,
        ];
        $person = static fn (string $prefix): array => [
            $text($prefix.'nama', 'නම'),
            $text($prefix.'samajika_ankaya', 'සාමාජික අංකය'),
            $text($prefix.'lipinaya1', 'ලිපිනය 1'),
            $text($prefix.'lipinaya2', 'ලිපිනය 2'),
            $text($prefix.'lipinaya3', 'ලිපිනය 3'),
        ];

        return [
            [
                'title' => 'නඩු',
                'fields' => [
                    $text('nadu_ankaya', 'නඩු අංකය', true),
                    $date('dun_dinaya', 'දුන් දිනය'),
                    $text('kalaya', 'කාලය'),
                ],
            ],
            ['title' => 'ණයකරු 1', 'fields' => $person('nayakaru1_')],
            ['title' => 'ණයකරු 2', 'fields' => $person('nayakaru2_')],
            ['title' => 'ඇපකරු 1', 'fields' => $person('aepakaru1_')],
            ['title' => 'ඇපකරු 2', 'fields' => $person('aepakaru2_')],
            [
                'title' => 'මූල්‍ය විස්තර',
                'fields' => [
                    $number('dun_naya_mudala', 'දුන් ණය මුදල'),
                    $number('arawul_mudala', 'ආරවුල් මුදල'),
                    $number('poli_prathishathaya', 'පොලී ප්‍රතිශතය'),
                    $date('awasan_mudal_bendima', 'අවසන් මුදල් බැඳීම'),
                    $number('dina_ganuna', 'දින ගණන', 'integer'),
                    $number('apal_pa', 'ඇපැල් පැ'),
                    $number('apal_vi', 'ඇපැල් වි'),
                    $number('mul_mudala', 'මුල් මුදල'),
                    $number('poliya', 'පොලිය'),
                    $number('nadu_gasthu', 'නඩු ගාස්තු'),
                    $number('total', 'එකතුව'),
                ],
            ],
        ];
    }
}
