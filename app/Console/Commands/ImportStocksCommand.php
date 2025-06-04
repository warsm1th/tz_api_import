<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Services\ApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportStocksCommand extends Command
{
    protected $signature = 'import:stocks {dateFrom : Дата выгрузки в формате Y-m-d}';

    protected $description = 'Импорт данных о складах из API в базу данных';

    public function handle(ApiService $apiService): int
    {
        $dateFrom = $this->argument('dateFrom');

        $this->info("Начало импорта данных склада на {$dateFrom}");

        try {
            $stocks = $apiService->getStocks($dateFrom);

            $bar = $this->output->createProgressBar(count($stocks));
            $bar->start();

            $chunks = array_chunk($stocks, 500);
            foreach ($chunks as $chunk) {
                $preparedData = array_map(function ($item) {
                    return [
                        'date' => $item['date'],
                        'last_change_date' => $item['last_change_date'],
                        'supplier_article' => $item['supplier_article'],
                        'tech_size' => $item['tech_size'],
                        'barcode' => $item['barcode'],
                        'quantity' => $item['quantity'],
                        'is_supply' => $item['is_supply'],
                        'is_realization' => $item['is_realization'],
                        'quantity_full' => $item['quantity_full'],
                        'warehouse_name' => $item['warehouse_name'],
                        'in_way_to_client' => $item['in_way_to_client'],
                        'in_way_from_client' => $item['in_way_from_client'],
                        'nm_id' => $item['nm_id'],
                        'subject' => $item['subject'],
                        'category' => $item['category'],
                        'brand' => $item['brand'],
                        'sc_code' => $item['sc_code'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }, $chunk);

                Stock::insert($preparedData);
                $bar->advance(count($chunk));
            }

            $bar->finish();
            $this->newLine();
            $this->info("Успешно импортировано " . count($stocks)) . " записей о складах";

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Ошибка при импорте данных склада: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
