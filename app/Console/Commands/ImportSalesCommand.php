<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Services\ApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportSalesCommand extends Command
{
    protected $signature = 'import:sales {dateFrom : Начальная дата в формате Y-m-d}
                                      {dateTo : Конечная дата в формате Y-m-d}';

    protected $description = 'Импорт данных о продажах из API в базу данных';

    public function handle(ApiService $apiService): int
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');

        $this->info("Начало импорта продаж с {$dateFrom} по {$dateTo}");

        try {
            $sales = $apiService->getSales($dateFrom, $dateTo);

            $bar = $this->output->createProgressBar(count($sales));
            $bar->start();

            $chunks = array_chunk($sales, 500);
            foreach ($chunks as $chunk) {
                $preparedData = array_map(function ($item) {
                    return [
                        'g_number' => $item['g_number'],
                        'date' => $item['date'],
                        'last_change_date' => $item['last_change_date'],
                        'supplier_article' => $item['supplier_article'],
                        'tech_size' => $item['tech_size'],
                        'barcode' => $item['barcode'],
                        'total_price' => $item['total_price'],
                        'discount_percent' => $item['discount_percent'],
                        'is_supply' => $item['is_supply'],
                        'is_realization' => $item['is_realization'],
                        'promo_code_discount' => $item['promo_code_discount'],
                        'warehouse_name' => $item['warehouse_name'],
                        'country_name' => $item['country_name'],
                        'oblast_okrug_name' => $item['oblast_okrug_name'],
                        'region_name' => $item['region_name'],
                        'income_id' => $item['income_id'],
                        'sale_id' => $item['sale_id'],
                        'odid' => $item['odid'],
                        'spp' => $item['spp'],
                        'for_pay' => $item['for_pay'],
                        'finished_price' => $item['finished_price'],
                        'price_with_disc' => $item['price_with_disc'],
                        'nm_id' => $item['nm_id'],
                        'subject' => $item['subject'],
                        'category' => $item['category'],
                        'brand' => $item['brand'],
                        'is_storno' => $item['is_storno'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }, $chunk);

                Sale::insert($preparedData);
                $bar->advance(count($chunk));
            }

            $bar->finish();
            $this->newLine();
            $this->info("Успешно импортировано " . count($sales) . " записей о продажах");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Ошибка при импорте продаж: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
