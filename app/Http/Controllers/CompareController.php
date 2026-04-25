<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Compare;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request, Compare $compare): View|RedirectResponse
    {
        $id = $request->input('category');

        if (! $id && session()->has('compare') && $compare->count()) {
            $firstCategory = array_key_first(session()->get('compare'));

            return redirect()->route('compare', ['category' => $firstCategory]);
        }

        $products = [];
        $categories = [];

        if ($compare->isCategoryExists($id)) {
            $products = $compare->getProductsByIds($compare->getCompareSession()[$id]);
            $categories = $compare->getExistsCategories();
        }

        return view('compare.index', compact('products', 'categories'));
    }

    public function add(Product $product, Compare $compare): JsonResponse
    {
        $compare->add($product);

        return $this->response($compare, __t('Товар успішно доданий у порівняння'),__t('Сравнение'));
    }

    public function _add(Product $product, Compare $compare): JsonResponse
    {
        echo "Start add";
        $product = Product::whereIn('code', ['16885','16886','16887','16888','16889','16890','16891','16892','16893','16894','16895','16896','16897'] )->get();
        //Product::where('id', 'in', [16885,16886,16887,16888,16889,16890,16891,16892,16893,16894,16895,16896,16897])->get();
        dump($product);
        foreach ($product as $pr){

            // dd(" **add** ",$product);
            $compare->add($pr);
        }
        dump("countByCategory",$compare->countByCategory());
        //$compare->add($product);

        return $this->response($compare, __t('Товар успішно доданий у порівняння'),__t('Сравнение'));
    }

    public function delete(Product $product, Compare $compare): JsonResponse
    {
        $compare->remove($product);

        return $this->response($compare, __t('Товар вилучений з порівняння'), __t('Сравнение'));
    }

    private function response(Compare $compare, string $message, string $title): JsonResponse
    {
        return response()->json(
            [
                'status' => 'success',
                'count' => $compare->count(),
                'message' => $message,
                'title' => $title,
            ]
        );
    }
}
