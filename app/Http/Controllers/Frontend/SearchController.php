<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute;
use DB;

class SearchController extends Controller
{
    private $record_per_page;
    public function __construct()
    {
        $this->record_per_page = 15;
        $this->middleware(["XssSanitizer"]);
    }

    public function products_search(Request $request)
    {
        $search_params = $request->all();
        $from_price = $search_params["from_price"];
        $to_price = $search_params["to_price"];
        $suitable_for = "";
        $categories = "";

        if (isset($search_params["suitableFor"])) {
            $suitable_for_temp_arr = [];

            foreach ($search_params["suitableFor"] as $suitableFor) {
                $suitable_for_temp_arr[] = getGroupIdFromSlug($suitableFor);
            }

            $suitable_for = implode(",", $suitable_for_temp_arr);
        }

        if (isset($search_params["categories"])) {
            $categories_temp_arr = [];
            foreach ($search_params["categories"] as $category) {
                $cat_id = Category::select("id")
                    ->where(["slug" => $category])
                    ->first();
                $categories_temp_arr[] = $cat_id["id"];
            }
            $categories = implode(",", $categories_temp_arr);
        }

        $sizes = isset($search_params["sizes"])
            ? implode(",", $search_params["sizes"])
            : "";

        $sql = "SELECT products.* FROM products";

        if ($categories != "") {
            $sql .=
                " JOIN product_categories ON product_categories.product_id = products.id";
        }

        if ($sizes != "") {
            $sql .=
                " JOIN product_sizes ON product_sizes.product_id = products.id";
        }

        $sql .= " WHERE products.status = 1 AND products.regular_price BETWEEN $from_price AND $to_price";

        if ($categories != "") {
            $sql .= " AND product_categories.category_id IN ($categories)";
        }

        if ($sizes != "") {
            $sql .= " AND product_sizes.size IN ('$sizes')";
        }

        if ($suitable_for != "") {
            $sql .= " AND products.suitable_for IN ($suitable_for)";
        }

        $sql .= " ORDER BY products.id DESC";
        $results = \Illuminate\Support\Facades\DB::select($sql);

        $results = $this->arrayPaginator($results, $request);

        $products = ($results);

        $search_results_arr = [
            "list_view" => view('frontend.pages.partials.productslist', compact('products'))->render()
        ];

        return $search_results_arr;
    }

    public function arrayPaginator($array, $request)
    {
        $page = $request->input('page', 1);
        $perPage = $this->record_per_page;
        $offset = ($page * $perPage) - $perPage;

        return new \Illuminate\Pagination\LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );
    }
    public function site_search(Request $request){
        $search_keyword = $request->get('keywords');

        // For AJAX live search
        if ($request->ajax()) {
            $products = Product::where('product_name', 'LIKE', '%' . $search_keyword . '%')
                ->orWhere('description', 'LIKE', '%' . $search_keyword . '%')
                ->limit(10)
                ->get();

            $output = '';
            if ($products->count() > 0) {
                foreach ($products as $product) {
                    $output .= '<div class="search-result-item">';
                    $output .= '<a href="' . route('main_product', $product->slug) . '">';
                    
                    // Product image
                    if ($product->product_image && file_exists(public_path('images/' . $product->product_image))) {
                        $output .= '<img src="' . asset('images/' . $product->product_image) . '" alt="' . $product->product_name . '">';
                    } else {
                        $output .= '<div class="no-image"><i class="fas fa-image"></i></div>';
                    }
                    
                    // Product info
                    $output .= '<div class="product-info">';
                    $output .= '<div class="product-name">' . $product->product_name . '</div>';
                    if ($product->description) {
                        $output .= '<div class="product-description">' . substr($product->description, 0, 60) . '...</div>';
                    }
                    $output .= '<div class="product-price">Rs. ' . number_format($product->regular_price) . '</div>';
                    $output .= '</div>';
                    
                    $output .= '</a>';
                    $output .= '</div>';
                }
            } else {
                $output .= '<div class="no-results">No products found</div>';
            }
            
            return $output;
        }

        // For regular search page
        $brands = Brand::where(function ($q) use ($search_keyword) {
            $q->orWhere("name", "LIKE", "%" . $search_keyword . "%");
            $q->orWhere("description", "LIKE", "%" . $search_keyword . "%");
        })->orderBy("created_at", "DESC")->get();
        
        $products = Product::where(function ($q) use ($search_keyword) {
            $q->orWhere("product_name", "LIKE", "%" . $search_keyword . "%");
            $q->orWhere("description", "LIKE", "%" . $search_keyword . "%");
        })->orderBy("created_at", "DESC")->get();
        
        return view('frontend.pages.search', compact('products','brands'));
    }

}
