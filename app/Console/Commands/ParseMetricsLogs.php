<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parser\IpsDevicesLogParser;
use App\Parser\TransitionsLogParser;

/**
 * Команда запускающая парсинг логов и сохранение данных в БД.
 */
class ParseMetricsLogs extends Command
{
    protected $signature = 'app:parse-metrics-logs';

    protected $description = 'Парсинг логов и сохранение в базу данных';

    private const TRANSITIONS_FILE_NAME = 'log-1';
    private const IPS_DEVICES_FILE_NAME = 'log-2';

    public function handle(): void
    {
        $transitionStatus = (new TransitionsLogParser(base_path(self::TRANSITIONS_FILE_NAME)))->parse();
        $ipsDevicesStatus = (new IpsDevicesLogParser(base_path(self::IPS_DEVICES_FILE_NAME)))->parse();

        $this->info($transitionStatus ? 'Статистика переходов сохранена в базу данных' : 'НЕ УДАЛОСЬ сохранить статистику переходов');
        $this->info($ipsDevicesStatus ? 'Статистика устройств сохранена в базу данных' : 'НЕ УДАЛОСЬ сохранить статистику устройств');
    }
}
