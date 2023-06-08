<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Preference;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Tag(
 *     name="Preference",
 *     description="Endpoints related to the Preference resource"
 * )
 */

class PreferenceController extends Controller
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
     *     path="/api/preferences",
     *     summary="Get the preferences",
     *     tags={"Preference"},
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="boolean"),
     *             @OA\Property(property="price", type="boolean"),
     *             @OA\Property(property="sku", type="boolean"),
     *             @OA\Property(property="details", type="boolean"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $preferences = $user->preference;
        return response()->json(['status' => true, 'data' => $preferences],200);
    }

    /**
     * @OA\Put(
     *     path="/api/preferences",
     *     summary="Update the preferences",
     *     tags={"Preference"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="boolean"),
     *             @OA\Property(property="price", type="boolean"),
     *             @OA\Property(property="sku", type="boolean"),
     *             @OA\Property(property="details", type="boolean"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation"
     *     )
     * )
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $preferences = $user->preference;
        $preferences->update($request->all());
        return response()->json(['status'=>true,'data' => [] ,'message' => 'Preferences updated successfully'],200);
    }


    
    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Store the preferences",
     *     tags={"Preference"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="boolean"),
     *             @OA\Property(property="price", type="boolean"),
     *             @OA\Property(property="sku", type="boolean"),
     *             @OA\Property(property="details", type="boolean"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $preferences = Preference::create($request->all());
        return response()->json(['status'=>true,'data' => ['preferences' => $preferences] ,'message' => 'Preferences created successfully'],200);
    }
}
