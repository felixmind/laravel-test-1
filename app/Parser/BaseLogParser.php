<?php

namespace App\Parser;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Базовый класс парсинга.
 * Отвечает за общий алгоритм работы парсера:
 * - обеспечение транзакционностью
 * - обработка ошибок
 * - последовательность действий
 *
 * Сами действия скрыты абстрактным методами (паттерн "шаблонный метод"),
 * которые нужно реализовать в дочерних классов под каждый файл-лог.
 */
abstract class BaseLogParser
{
    protected $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return bool
     * @throws Throwable
     */
    public function parse(): bool
    {
        DB::beginTransaction();

        try {
            $this->run();
        } catch (Throwable $t) {
            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }

    private function run(): void
    {
        $file = fopen($this->filePath, 'rb');

        // Обрабатываем построчно, чтобы не загружать память если логи будут большими.
        while ($row = fgets($file)) {
            try {
                $values = $this->extractValues(trim($row));
            } catch (Throwable $t) {
                // Пропускаем запись, если при извлечении данных произойдёт ошибка.
                continue;
            }

            $validator = $this->createValidator($values);
            if ($validator->fails()) {
                // Пропускаем запись, если данные некорректны.
                continue;
            }

            $this->saveValuesToDB($values);
        }

        fclose($file);
    }

    /**
     * Метода извлекает значения из строки логов.
     *
     * @param string $row
     *
     * @return array
     */
    abstract protected function extractValues(string $row): array;

    /**
     * Метод создаёт валидатор для проверки значений.
     *
     * @param array $values
     *
     * @return Validator
     */
    abstract protected function createValidator(array $values): Validator;

    /**
     * Метод сохраняет значения в базу данных.
     *
     * @param array $values
     */
    abstract protected function saveValuesToDB(array $values): void;
}
