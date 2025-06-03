<?php

namespace App\Console\Commands;

use App\Models\Income;
use App\Services\ApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportIncomesCommand extends Command
{
    protected $signature = 'import:incomes {dateFrom : Начальная дата в формате Y-m-d}
                                      {dateTo : Конечная дата в формате Y-m-d}';

    protected $description = 'Импорт данных о доходах из API в базу данных';

    public function handle(ApiService $apiService): int
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');

        $this->info("Начало импорта доходов с {$dateFrom} по {$dateTo}");

        try {
            $incomes = $apiService->getIncomes($dateFrom, $dateTo);

            $bar = $this->output->createProgressBar(count($incomes));
            $bar->start();

            $chunks = array_chunk($incomes, 500);
            foreach ($chunks as $chunk) {
                $preparedData = array_map(function ($item) {
                    return [
                        'income_id' => $item['income_id'],
                        'number' => $item['number'],
                        'date' => $item['date'],
                        'last_change_date' => $item['last_change_date'],
                        'supplier_article' => $item['supplier_article'],
                        'tech_size' => $item['tech_size'],
                        'barcode' => $item['barcode'],
                        'quantity' => $item['quantity'],
                        'total_price' => $item['total_price'],
                        'date_close' => $item['date_close'],
                        'warehouse_name' => $item['warehouse_name'],
                        'nm_id' => $item['nm_id'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }, $chunk);

                Income::insert($preparedData);
                $bar->advance(count($chunk));
            }

            $bar->finish();
            $this->newLine();
            $this->info("Успешно импортировано " . count($incomes) . " записей о доходах");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Ошибка при импорте доходов: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
