<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrderItems;
use App\Models\ComboProduct;
use App\Models\ComboProductRating;
use App\Models\Seller;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class ComboProductRatingController extends Controller
{
    public function set_rating(Request $request, $files)
    {
        $data = $request->all();

        $rating = [
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
        ];

        if (isset($data['rating']) && !empty($data['rating'])) {
            $rating['rating'] = $data['rating'];
        }

        if (isset($data['comment']) && !empty($data['comment'])) {
            $rating['comment'] = $data['comment'];
        }

        if (isset($data['title']) && !empty($data['title'])) {
            $rating['title'] = $data['title'];
        }

        if ($files) {
            foreach ($files as $file) {
                if (is_array($file)) {
                    // If $file is an array, you need to iterate through its contents
                    foreach ($file as $f) {
                        $uploadedImage = $this->uploadFile($f);
                        $uploadedImages[] = $uploadedImage;
                    }
                } else {
                    // Handle the single file object
                    $uploadedImage = $this->uploadFile($file);
                    $uploadedImages[] = $uploadedImage;
                }
            }
        }
        $rating['images'] = isset($uploadedImages) && !empty($uploadedImages) ? json_encode($uploadedImages) : '';

        // Every newly submitted or edited review re-enters moderation as pending
        // (0 = pending). It stays unpublished until an admin approves it.
        $rating['status'] = 0;

        $existing_rating = ComboProductRating::where('user_id', $data['user_id'])
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing_rating) {
            $existing_rating->update($rating);
        } else {
            ComboProductRating::create($rating);
        }

        // Recompute aggregates from approved reviews only. A freshly saved pending
        // review therefore does not affect the combo/seller rating until approved.
        $this->recalculateAggregates($data['product_id']);

        return true;
    }

    /**
     * Recalculate a combo product's stored average rating / count and its seller's
     * aggregate rating, counting APPROVED reviews (status = 1) only.
     */
    public function recalculateAggregates($product_id)
    {
        $product = ComboProduct::find($product_id);

        if (!$product) {
            return;
        }

        $ratings = ComboProductRating::where('product_id', $product_id)->where('status', 1)->count();
        $total_rating = ComboProductRating::where('product_id', $product_id)->where('status', 1)->sum('rating');
        $new_rating = ($ratings > 0) ? round($total_rating / $ratings, 1, PHP_ROUND_HALF_UP) : 0;
        $product->update(['rating' => $new_rating, 'no_of_ratings' => $ratings]);

        // Update seller rating (scoped to the combo product's store pivot).
        $store_id = $product->store_id;
        $seller_id = $product->seller_id;
        $seller_ratings = ComboProduct::where('seller_id', $seller_id)->where('rating', '>', 0)->count();
        $seller_total_rating = ComboProduct::where('seller_id', $seller_id)->sum('rating');
        $seller_new_rating = ($seller_ratings > 0) ? round($seller_total_rating / $seller_ratings, 1, PHP_ROUND_HALF_UP) : 0;

        $seller = Seller::find($seller_id);
        if ($seller) {
            $seller->stores()->updateExistingPivot($store_id, [
                'rating' => $seller_new_rating,
                'no_of_ratings' => $seller_ratings
            ]);
        }
    }


    private function uploadFile($file)
    {
        $image_original_name = $file->getClientOriginalName();
        $image = Storage::disk('public')->putFileAs('review_images', $file, $image_original_name);
        return $image;
    }

    public function fetch_rating($product_id = '', $user_id = '', $limit = '', $offset = '', $sort = 'id', $order = 'desc', $rating_id = '', $has_images = '', $rating = '', $count_empty_comments = false, $status = '')
    {
        // Fetch product ratings with user data
        $query = ComboProductRating::with(['user:id,username,image'])
            ->when($product_id, fn($q) => $q->where('product_id', $product_id))
            ->when($user_id, fn($q) => $q->where('user_id', $user_id))
            ->when($rating_id, fn($q) => $q->where('id', $rating_id))
            ->when($rating, fn($q) => $q->where('rating', $rating))
            ->when($status !== '' && $status !== null, fn($q) => $q->where('status', $status))
            ->when($has_images == 1, fn($q) => $q->whereNotNull('images'))
            ->when($sort && $order, fn($q) => $q->orderBy($sort, $order))
            ->skip($offset)
            ->take($limit);

        $rating_data = $query->get();

        // Transform images and user data
        $rating_data = $rating_data->transform(function ($rating) {
            $ratingArray = $rating->toArray(); // convert to raw array early

            // Format dates
            $ratingArray['created_at'] = $rating->created_at ? $rating->created_at->format('Y-m-d H:i:s') : null;
            $ratingArray['updated_at'] = $rating->updated_at ? $rating->updated_at->format('Y-m-d H:i:s') : null;

            // Decode images
            $ratingArray['images'] = $rating->images ? array_map(fn($img) => asset('storage/' . $img), json_decode($rating->images, true)) : [];

            // Add user data
            $ratingArray['user_profile'] = $rating->user?->image ? asset(config('constants.USER_IMG_PATH') . $rating->user->image) : null;
            $ratingArray['user_name'] = $rating->user?->username ?? null;
            unset($ratingArray['user']);
            return $ratingArray;
        })->toArray();

        // Aggregates
        $res = [];

        // Aggregate stats follow the same status scope as the list (e.g. mobile API
        // passes status = 1 so counts reflect published reviews only).
        $statusScope = fn($q) => ($status !== '' && $status !== null) ? $q->where('status', $status) : $q;

        // Total number of ratings
        $res['no_of_rating'] = ComboProductRating::where('product_id', $product_id)->where($statusScope)->count();

        // Total reviews with images
        $res['total_images'] = (string) ComboProductRating::where('product_id', $product_id)->where($statusScope)
            ->whereNotNull('images')
            ->get()
            ->sum(function ($item) {
                $decoded = json_decode($item->images, true);
                return is_array($decoded) ? count($decoded) : 0;
            });

        // Star-wise review breakdown
        $star_counts = ComboProductRating::selectRaw('
                count(id) as total,
                sum(case when CEILING(rating) = 1 then 1 else 0 end) as rating_1,
                sum(case when CEILING(rating) = 2 then 1 else 0 end) as rating_2,
                sum(case when CEILING(rating) = 3 then 1 else 0 end) as rating_3,
                sum(case when CEILING(rating) = 4 then 1 else 0 end) as rating_4,
                sum(case when CEILING(rating) = 5 then 1 else 0 end) as rating_5
        ')
            ->where('product_id', $product_id)
            ->where($statusScope)
            ->first();

        $res['total_reviews'] = $star_counts->total ?? 0;
        $res['star_1'] = $star_counts->rating_1 ?? 0;
        $res['star_2'] = $star_counts->rating_2 ?? 0;
        $res['star_3'] = $star_counts->rating_3 ?? 0;
        $res['star_4'] = $star_counts->rating_4 ?? 0;
        $res['star_5'] = $star_counts->rating_5 ?? 0;

        // Reviews with non-empty comments
        $res['no_of_reviews'] = $count_empty_comments
            ? ComboProductRating::where('product_id', $product_id)->where($statusScope)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->count()
            : 0;

        // Final product rating data
        $res['product_rating'] = $rating_data;

        return $res;
    }


    public function delete_rating($rating_id)
    {

        $rating_id = (int) $rating_id;
        $rating_details = ComboProductRating::find($rating_id);

        if ($rating_details) {
            $images = json_decode($rating_details->images, true);

            if (!empty($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $product_id = $rating_details->product_id;

            $rating_details->delete();

            // Recompute combo + seller aggregates from approved reviews only.
            $this->recalculateAggregates($product_id);

            return true;
        } else {
            return false;
        }
    }
}
