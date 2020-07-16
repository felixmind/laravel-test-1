<?php

namespace App\Parser;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Класс обработки логов переходов.
 */
class TransitionsLogParser extends BaseLogParser
{
    /**
     * @inheritDoc
     */
    protected function extractValues(string $row): array
    {
        [$date, $time, $ip, $urlFrom, $urlTo] = explode('|', $row, 5);
        $datetime = "{$date} {$time}";

        $occurredAt = Carbon::createFromTimeString($datetime)->toDateTimeString();

        return compact('occurredAt', 'ip', 'urlFrom', 'urlTo');
    }

    /**
     * @inheritDoc
     */
    protected function createValidator(array $values): Validator
    {
        return validator(
            [$values['occurredAt'], $values['ip'], $values['urlFrom'], $values['urlTo']],
            ['date_format:Y-m-d H:i:s', 'ip', 'url', 'url']
        );
    }

    /**
     * @inheritDoc
     */
    protected function saveValuesToDB(array $values): void
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('transitions')
          ->insert([
             'occurred_at' => $values['occurredAt'],
             'ip'          => $values['ip'],
             'url_from'    => $values['urlFrom'],
             'url_to'      => $values['urlTo'],
             'created_at'  => $now,
             'updated_at'  => $now,
          ]);
    }
}