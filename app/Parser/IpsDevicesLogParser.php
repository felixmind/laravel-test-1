<?php

namespace App\Parser;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Класс обработки логов устройств пользователей.
 */
class IpsDevicesLogParser extends BaseLogParser
{
    /**
     * @inheritDoc
     */
    protected function extractValues(string $row): array
    {
        [$ip, $browser, $os] = explode('|', $row, 3);

        return compact('ip', 'browser', 'os');
    }

    /**
     * @inheritDoc
     */
    protected function createValidator(array $values): Validator
    {
        return validator([$values['ip']], ['ip']);
    }

    /**
     * @inheritDoc
     */
    protected function saveValuesToDB(array $values): void
    {
        $now = Carbon::now()->toDateTimeString();

        $values['created_at'] = $now;
        $values['updated_at'] = $now;

        DB::table('ips_devices')
          ->insert($values);
    }
}
