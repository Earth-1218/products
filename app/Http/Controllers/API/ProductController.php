<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Response;
use League\Csv\Reader;
use DataTables;
use Auth;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(
 *     title="Product API",
 *     version="1.0.0",
 *     description="API endpoints for managing products",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 *    
 * ),
 * @OA\Tag(
 *     name="Product",
 *     description="Endpoints related to the Product resource"
 * )
 */

class ProductController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get a list of products",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of products per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10,
     *             minimum=1,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Column to sort the products by",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Search term to filter products by name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="sku",
     *         in="query",
     *         description="Search term to filter products by SKU",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="details",
     *         in="query",
     *         description="Search term to filter products by details",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     ),
     * )
     */
    
     public function index(Request $request)
     {          
         $user = Auth::user();
         $default_preferences = (object)[
            'name' => 1,
            'sku' => 1,
            'details' => 1,
            'price' => 1,
            'status' => 1
         ];
         
         $preferences =  (isset($user->preference)) ?  $user->preference :  $default_preferences;
         $query = Product::query();

         // Apply searching
        
        if ($request->has('name') && $request->has('sku') && $request->has('details') && $request->has('price')) {
                $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
                $query->where('sku', 'LIKE', '%' . $request->input('sku') . '%');
                $query->where('details', 'LIKE', '%' . $request->input('details') . '%');
                $query->where('price', 'LIKE', '%' . $request->input('price') . '%');
        }

         // Apply column-wise sorting
        if ($request->has('order') && $request->has('columns')) {
            $columns = $request->input('columns');
            $order = $request->input('order');

            foreach ($columns as $index => $column) {
                if (isset($column['name']) && $column['name'] !== 'action') {
                    $columnName = $column['name'];
                    $dir = $order[0]['dir']; // Get the sorting direction from the 'dir' property
                    $sorting_column  = $order[0]['column'];
                    if($sorting_column == $index)
                    {
                        if ($index === 2 || $index === 3 || $index === 4) {
                            $query->orderByRaw("LOWER($columnName) $dir");
                        } else if ($index === 5 || $index === 1) {
                            $query->orderByRaw("CONVERT($columnName, DECIMAL) $dir");
                        } else {
                            $query->orderBy($columnName, $dir);
                        }
                    }
                }
            }         
        }


        
         
         $datatable = DataTables::of($query)
            ->addColumn('is_checked', function ($product) {
                    return $product->id;
            })
             ->addColumn('id', function ($product) {
                return $product->id;
             })
             ->addColumn('status', function ($product) {
                 $badge = ($product->status == 'inactive') ? 'badge badge-danger' : 'badge badge-success';
                 return '<span class="' . $badge . '">' . $product->status . '</span>';
             })
             ->addColumn('actions', function ($product) {
                 return '<div class="d-flex">
                     <button class="btn btn-primary" onclick="editproduct(' . $product->id . ')"><i class="fa fa-edit"></i></button>
                     &nbsp;
                     <button class="btn btn-danger" onclick="deleteproduct(' . $product->id . ')"><i class="fa fa-trash"></i></button>
                 </div>';
             })
             ->rawColumns(['status', 'actions']);
         
         // Include additional columns based on preferences
         if ($preferences->name == 1) {
             $datatable->addColumn('name', function ($product) {
                 return $product->name;
             });
         }
        //   else {
        //      $datatable->removeColumn('name');
        //  }
         if ($preferences->price == 1) {
             $datatable->addColumn('price', function ($product) {
                 return $product->price;
             });
         } 
        //  else {
        //      $datatable->removeColumn('price');
        //  }
         if ($preferences->sku == 1) {
             $datatable->addColumn('sku', function ($product) {
                 return $product->sku;
             });
         } 
        //  else {
        //      $datatable->removeColumn('sku');
        //  }

         if ($preferences->details == 1) {
             $datatable->addColumn('details', function ($product) {
                 return $product->details;
             });
         } 
        //  else {
        //      $datatable->removeColumn('details');
        //  }
         
         $dataTableResult = $datatable->make(true);
         return $dataTableResult;
     }
     


    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Product"}, 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Product Name"),
     *             @OA\Property(property="sku", type="string", example="ABC123"),
     *             @OA\Property(property="details", type="string", example="Product details"),
     *             @OA\Property(property="price", type="number", format="float", example=9.99),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
     *             @OA\Property(property="is_deleted", type="boolean", example=false, default=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object"),
     *         ),
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sku' => 'required|unique:products',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::create($request->all());
        return response()->json(['status' => true ,'data' =>$product], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a product by ID",
     *     tags={"Product"}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         ),
     *     ),
     * )
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['status' => true ,'data' => $product],200);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product by ID",
     *     tags={"Product"}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object"),
     *         ),
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        // print_r($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sku' => [
                'required',
                Rule::unique('products')->ignore($id),
            ],
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json(['status' => true ,'data' => $product],200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product by ID",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="No content",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         ),
     *     ),
     * )
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        // $product->is_deleted = true;
        // $product->save();
        return response()->json(['status' => true ,'data' => [], 'messsage' => 'Product Deleted !'], 204);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->selected_product_ids;
        $products = new Product();
        $products->whereIn('id', $ids)->delete();
        return response()->json(['status' => true ,'data' => [], 'messsage' => 'Products Deleted !'], 200);
    }



    public function export()
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
    
        $products = Product::all();
        $columns = array('id', 'name', 'sku', 'price', 'details', 'status');
    
        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach ($products as $product) {
                fputcsv($file, array($product->id, $product->name, $product->sku, $product->price, $product->details, $product->status));
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }
    


    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'products_list' => 'required|mimes:csv,txt'
        ]);

        // Process the imported file
        if ($request->hasFile('products_list')) {
            $file = $request->file('products_list');

            // Create a CSV reader instance
            $reader = Reader::createFromPath($file->path());
            
            // Skip the header row
            $reader->setHeaderOffset(0);
            // Create a product from each row in the CSV file
         
            foreach ($reader as  $row) {
                // dd($row);
                $is_exists = DB::table('products')
                                    //  ->where('id',$row['id'])
                                     ->orWhere('name',$row['name'])
                                    //  ->orWhere('details',$row['details'])
                                     ->orWhere('sku',$row['sku'])
                                    //  ->orWhere('price',$row['price'])
                                    //  ->orWhere('status',$row['status'])
                                     ->exists();
                if(!$is_exists) 
                {                    
                    Product::create($row);  
                }
            }
            
            return redirect()->back()->with('success', 'Import completed successfully.');
        }

    }


    public function truncate()
    {
        DB::table('products')->truncate();
        return response()->json(['status' => true ,'data' => [], 'message' => 'Product table truncated'],201);
    }
}

