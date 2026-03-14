<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReviewImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_reviews_images';

    protected $fillable = [
        'customer_review_id',
        'image_path',
    ];

    public function review()
    {
        return $this->belongsTo(CustomerReview::class, 'customer_review_id');
    }
}
