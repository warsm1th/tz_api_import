<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\ApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportOrdersCommand extends Command
{
    protected $signature = 'import:orders {dateFrom : Начальная дата в формате Y-m-d}
                                      {dateTo : Конечная дата в формате Y-m-d}';

    protected $description = 'Импорт данных о заказах из API в базу данных';

    public function handle(ApiService $apiService):int
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');

        $this->info("Начало импорта заказов с {$dateFrom} по {$dateTo}");

        try {
            $orders = $apiService->getOrders($dateFrom, $dateTo);

            $bar = $this->output->createProgressBar(count($orders));
            $bar->start();

            $chunks = array_chunk($orders, 500);
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
                        'warehouse_name' => $item['warehouse_name'],
                        'oblast' => $item['oblast'],
                        'income_id' => $item['income_id'],
                        'odid' => $item['odid'],
                        'nm_id' => $item['nm_id'],
                        'subject' => $item['subject'],
                        'category' => $item['category'],
                        'brand' => $item['brand'],
                        'is_cancel' => $item['is_cancel'],
                        'cancel_dt' => $item['cancel_dt'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }, $chunk);

                Order::insert($preparedData);
                $bar->advance(count($chunk));
            }

            $bar->finish();
            $this->newLine();
            $this->info("Успешно импортировано " . count($orders) . " записей о заказах");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Ошибка при импорте заказов: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
