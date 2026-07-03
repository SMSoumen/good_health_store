<?php

namespace App\Livewire\Pages;

use App\Models\ComboProduct;
use App\Models\ComboProductRating;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerRatings extends Component
{
    use WithFileUploads;

    protected $listeners = ['updateRating'];

    public $user_id;
    public function __construct()
    {
        $this->user_id = Auth::user() != '' ? Auth::user()->id : NUll;
    }

    public $product_details;

    public $product_id = "";

    public $product_type = "";

    public $review_id;

    public $comment = "";

    public $title = "";

    public $images = [];

    public $rating;

    public $is_disabled = false;

    public function render()
    {
        $product_details = $this->product_details;
        $this->product_type = $product_details->type;
        if ($product_details->type == "combo-product") {
            $user_review = fetchDetails(ComboProductRating::class, ['user_id' => $this->user_id, "product_id" => $product_details->id]);
        } else {
            $user_review = fetchDetails(ProductRating::class, ['user_id' => $this->user_id, "product_id" => $product_details->id]);
        }
        $this->review_id = $user_review[0]->id ?? "";
        $this->product_id = $product_details->id ?? "";

        if (isset($this->review_id) && !empty($this->review_id)) {
            $this->rating = (isset($user_review[0]->rating)) ? $user_review[0]->rating : "";

            $this->comment = (isset($user_review[0]->comment)) ? $user_review[0]->comment : "";

            $this->title = (isset($user_review[0]->title)) ? $user_review[0]->title : "";
        }

        $product_ratings = $this->getProductRating($this->product_id, $product_details->type);
        foreach ($product_ratings as $key => $ratings) {
            $user_profile = fetchDetails(User::class, ['id' => $ratings->user_id], ['image', 'username']);
            $product_ratings[$key]->user_profile = $user_profile[$key]->image ?? "";
            $product_ratings[$key]->user_name = $user_profile[$key]->username ?? "";
        }

        $sortedReviews = $this->sortReviews($product_ratings, $this->user_id);
        $sortedReviews = array_slice($sortedReviews, 0, 3);
        return view('components.utility.others.customer-ratings', [
            'customer_reviews' => $sortedReviews,
            'product_details' => $this->product_details
        ]);
    }

    public function sortReviews($reviews, $UserId)
    {
        $sortedReviews = [];
        foreach ($reviews as $review) {
            if ($review->user_id == $UserId) {
                array_unshift($sortedReviews, $review);
            } else {
                $sortedReviews[] = $review;
            }
        }
        return $sortedReviews;
    }

    public function getProductRating($product_id, $type)
    {
        // Show admin-approved reviews to everyone, plus the current user's own
        // review (even while pending) so they can see its moderation state.
        $ratingModel = $type == 'combo-product' ? ComboProductRating::class : ProductRating::class;
        $product_ratings = $ratingModel::where('product_id', $product_id)
            ->where(function ($q) {
                $q->where('status', 1);
                if (!empty($this->user_id)) {
                    $q->orWhere('user_id', $this->user_id);
                }
            })
            ->get();
        return $product_ratings;
    }

    public function updateRating($update_rating)
    {
        $this->rating = $update_rating;
    }

    public function save_review()
    {
        if ($this->is_disabled == false) {
            $validator = Validator::make(
                [
                    'rating' => $this->rating,
                    'title' => $this->title,
                    'comment' => $this->comment,
                    'images.*' => $this->images,
                ],
                [
                    'rating' => 'required',
                    'title' => 'required',
                    'comment' => 'required',
                    'images.*' => 'image|max:2048'
                ],
                [
                    'comment' => 'Please Write a Review'
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors();
                $this->dispatch('validationErrorshow', ['data' => $errors]);
                $response['error'] = true;
                $response['message'] = $errors;
                return $response;
            }
            $images = [];
            foreach ($this->images as $key => $image) {
                $imageName = 'image_' . time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                $review_image = $image->storeAs('review_image', $imageName, 'public');
                array_push($images, $review_image);
            }
            $validated['product_id'] = $this->product_id;
            $validated['title'] = $this->title;
            $validated['rating'] = $this->rating;
            $validated['comment'] = $this->comment;
            $validated['user_id'] = Auth::user()->id;

            // Both product and combo reviews require admin approval before being
            // published. Submitting or editing (re)sets the review to pending (0).
            $validated['status'] = 0;

            if ($this->review_id) {
                if ($this->product_type == "combo-product") {
                    $existingReview = ComboProductRating::findOrFail($this->review_id);
                } else {
                    $existingReview = ProductRating::findOrFail($this->review_id);
                }
                if (empty($images)) {
                    $validated['images'] = $existingReview['images'];
                } else {
                    $validated['images'] = $images;
                    $existingReview['images'] = json_decode($existingReview['images']);
                    foreach ($existingReview['images'] as $existingImage) {
                        if (Storage::exists("public/" . $existingImage)) {
                            Storage::delete("public/" . $existingImage);
                        }
                    }
                }
                $existingReview->update($validated);
                $this->dispatch('showSuccess', 'Your review has been submitted and is awaiting approval.');
            } else {
                $validated['images'] = json_encode($images);
                if ($this->product_type == "combo-product") {
                    ComboProductRating::create($validated);
                } else {
                    ProductRating::create($validated);
                }
                $this->dispatch('showSuccess', 'Your review has been submitted and is awaiting approval.');
                $this->is_disabled = true;
            }
            // A pending review must not affect the published aggregate; recompute
            // strictly from approved reviews.
            $this->recalculateAggregate($validated['product_id'], $this->product_type);
            return;
        }
    }

    public function delete_rating()
    {
        if ($this->review_id) {
            if ($this->product_type == "combo-product") {
                $existingReview = ComboProductRating::findOrFail($this->review_id);
            } else {
                $existingReview = ProductRating::findOrFail($this->review_id);
            }

            $delete = $existingReview->delete();
            $this->dispatch('showSuccess', 'The review has been successfully removed.');
            $this->is_disabled = false;
            $this->recalculateAggregate($this->product_id, $this->product_type);
        }
    }

    /**
     * Recompute a product's (or combo product's) published average rating and
     * review count from APPROVED reviews only (status = 1).
     */
    private function recalculateAggregate($product_id, $type)
    {
        $isCombo = $type == "combo-product";
        $ratingModel = $isCombo ? ComboProductRating::class : ProductRating::class;
        $productModel = $isCombo ? ComboProduct::class : Product::class;

        $approvedCount = $ratingModel::where('product_id', $product_id)->where('status', 1)->count();
        $averageRating = $approvedCount > 0
            ? $ratingModel::where('product_id', $product_id)->where('status', 1)->avg('rating')
            : 0;

        updateDetails([
            'rating' => $averageRating,
            'no_of_ratings' => $approvedCount,
        ], ['id' => $product_id], $productModel);
    }
}
