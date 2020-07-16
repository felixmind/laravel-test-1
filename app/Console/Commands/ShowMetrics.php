<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Команда для вывода метрик по IP-адресам пользователей.
 */
class ShowMetrics extends Command
{
    protected $signature = 'app:show-metrics';

    protected $description = 'Вывод метрик';

    public function handle(): void
    {
        $headers = [
            'IP',
            'Browser',
            'OS',
            'First URL (from)',
            'Last URL (to)',
            'Unique URLs',
        ];

        $this->table($headers, $this->getTableRows());
    }

    /**
     * Возвращает записи для вывода в таблицу.
     *
     * Считаем что последний зарегистрированный барузер и ОС на IP
     * является текущими браузером и ОС пользователя.
     *
     * Под уникальными сайтами считаются сайты НА которые перешёл пользователь.
     *
     * @return array
     */
    private function getTableRows(): array
    {
        return DB::select(DB::raw('
              SELECT transitions.ip, unique_devices.browser, unique_devices.os,
                     (
                         SELECT fut.url_from
                         FROM transitions as fut
                         WHERE fut.ip = transitions.ip
                         ORDER BY id
                         LIMIT 1
                     ) as first_url_from,
                     (
                         SELECT lut.url_to
                         FROM transitions as lut
                         WHERE lut.ip = transitions.ip
                         ORDER BY id DESC
                         LIMIT 1
                     ) as last_url_to,
                     COUNT(DISTINCT url_to) AS unique_views
              FROM transitions
              LEFT JOIN (
                  SELECT DISTINCT ON (ips_devices.ip) ips_devices.*
                  FROM ips_devices
                  ORDER BY ips_devices.ip, ips_devices.id DESC
                  ) AS unique_devices ON transitions.ip = unique_devices.ip
              GROUP BY transitions.ip, unique_devices.browser, unique_devices.os
        '));
    }
}
