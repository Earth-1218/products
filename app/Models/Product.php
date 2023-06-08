<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations\Schema;

/**
 * @OA\Schema(
 *     schema="Product",
 *     required={"name", "sku", "price", "status"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="sku", type="string"),
 *     @OA\Property(property="details", type="string"),
 *     @OA\Property(property="price", type="number"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}),
 *     @OA\Property(property="is_deleted", type="boolean")
 * )
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['id','name', 'sku', 'details', 'price', 'status', 'is_deleted'];

}
