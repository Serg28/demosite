<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Jobs\MaintenanceCategories;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder as RB;

class CategoryController extends Controller
{
    /**
     * Получить список всех категорий
     *
     * @input bool $request - показывать ли пагинацию
     *
     */
    public function index(Request $request)
    {
        $paginate = (bool)$request->input('paginate', true);

        if ($paginate !== false) {
            $data = Category::fastPaginate();
            $data->getCollection()->transform(function ($product) {
                return new CategoryResource($product);
            });
        } else {
            $data = Category::all();
            $data->transform(function ($product) {
                return new CategoryResource($product);
            });
        }

        return RB::success($data);
    }

    public function store(Request $request)
    {
    }


    /**
     * Получение категории
     *
     * @param Category $category ID запрашиваемой категории
     * @response CategoryResource
     */
    public function show(Category $category)
    {
        try {
            $data = new CategoryResource($category);
            return RB::success($data);
        } catch (\Exception $exception) {
            return RB::asError()->withMessage('CategoryController: ' . $exception->getMessage())->build();
        }
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    /**
     * Удаление всех категорий
     *
     * Полное удаление всех категорий из таблицы 'categories' и подготовка новой пустой структуры категорий.
     *
     * ОСТОРОЖНО!!! У всех товаров, находящихся в удаленных категориях, будут сброшены связи с этими категориями
     *
     */
    public function truncate()
    {
        try {
            // Delete all records from the 'categories' table
            \DB::table('categories')->delete();

            // Reset the AUTO_INCREMENT value to 1 for 'categories' table
            \DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');

            // Insert new record into 'categories' table
            \DB::table('categories')->insert([
                'id' => 1,
                'parent_id' => null,
                'title' => '{"ua":"Головна","ru":"Главная"}',
                'slug' => '/',
                'url' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            MaintenanceCategories::dispatch();
            return RB::success();
        } catch (\Exception $exception) {
            return RB::asError()->withMessage('CategoryController: ' . $exception->getMessage())->build();
        }
    }

    /**
     * Перестройка дерева категорий
     *
     * Запуск фонового процесса перестройки, исправления и восстановления корректности иерархии категорий товаров в таблице 'categories'.
     *
     * Этот метод следует запускать всякий раз, когда в таблице 'categories' прямым запросом добавляется или удаляется категория.
     *
     */
    public function rebuild()
    {
        MaintenanceCategories::dispatch();

        //Log::info('CategoryController started');
        return RB::asSuccess()->withMessage('CategoryController: начат фоновый процесс перестройки категорий')->build();
    }
}
