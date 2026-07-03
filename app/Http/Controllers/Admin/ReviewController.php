<?php

namespace App\Http\Controllers\Admin;

use App\Models\ComboProductRating;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * Human-readable status labels for the moderation dropdown.
     * 0 = pending, 1 = approved, 2 = rejected.
     */
    private array $statuses = [
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Rejected',
    ];

    /**
     * Per-type configuration so one controller can moderate both regular product
     * reviews and combo-product reviews.
     */
    private function config(string $type): array
    {
        if ($type === 'combo') {
            return [
                'type' => 'combo',
                'model' => ComboProductRating::class,
                'rating_controller' => ComboProductRatingController::class,
                'show_route' => 'admin.combo_products.show',
                'table_id' => 'admin_combo_reviews_table',
                'title' => labels('admin_labels.combo_product_reviews', 'Combo Product Reviews'),
                'ids_table' => 'combo_product_ratings',
            ];
        }

        return [
            'type' => 'product',
            'model' => ProductRating::class,
            'rating_controller' => ProductRatingController::class,
            'show_route' => 'admin.product.show',
            'table_id' => 'admin_reviews_table',
            'title' => labels('admin_labels.product_reviews', 'Product Reviews'),
            'ids_table' => 'product_ratings',
        ];
    }

    /**
     * Resolve the active type from the route default / query string.
     */
    private function currentType(): string
    {
        return request('type') === 'combo' ? 'combo' : 'product';
    }

    public function index($type = 'product')
    {
        $cfg = $this->config($type === 'combo' ? 'combo' : 'product');

        return view('admin.pages.forms.review_moderation', [
            'type' => $cfg['type'],
            'page_title' => $cfg['title'],
            'table_id' => $cfg['table_id'],
            'list_url' => route('admin.reviews.list', ['type' => $cfg['type']]),
            'delete_url' => route('admin.reviews.delete', ['type' => $cfg['type']]),
        ]);
    }

    public function list()
    {
        $cfg = $this->config($this->currentType());
        $model = $cfg['model'];

        $search = trim(request('search'));
        $sort = request('sort') ?: 'id';
        $order = request('order') ?: 'DESC';
        $offset = $search || request('pagination_offset') ? request('pagination_offset') : 0;
        $limit = request('limit') ?: '10';

        $query = $model::with(['user:id,username,image', 'product:id,name'])
            ->when($search, function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('username', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"));
            });

        $total = $query->count();

        $reviews = $query->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($r) use ($cfg) {
                $delete_url = route('admin.reviews.destroy', ['id' => $r->id, 'type' => $cfg['type']]);
                $product_url = route($cfg['show_route'], $r->product_id);
                $status_url = route('admin.reviews.update_status', ['id' => $r->id, 'type' => $cfg['type']]);

                $action = '<div class="dropdown bootstrap-table-dropdown">
                    <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-horizontal-rounded"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item dropdown_menu_items" href="' . $product_url . '" target="_blank"><i class="bx bx-link-external mx-2"></i> ' . labels('admin_labels.view_product', 'View Product') . '</a>
                        <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> ' . labels('admin_labels.delete', 'Delete') . '</a>
                    </div>
                </div>';

                // Moderation dropdown. The global .change_toggle_status handler
                // posts the selected value to update_status via GET.
                $status = (int) $r->status;
                $select = '<select class="form-select change_toggle_status ' . ($status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $r->id . '" data-url="' . $status_url . '">';
                foreach ($this->statuses as $value => $label) {
                    $select .= '<option value="' . $value . '" ' . ($status === $value ? 'selected' : '') . '>' . labels('admin_labels.review_status_' . $value, $label) . '</option>';
                }
                $select .= '</select>';

                // Star rating display.
                $stars = '';
                $rounded = (int) round($r->rating);
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= '<i class="bx bxs-star ' . ($i <= $rounded ? 'text-warning' : 'text-muted') . '"></i>';
                }

                // Review image thumbnails.
                $images = json_decode($r->images, true);
                $thumbs = '';
                if (is_array($images)) {
                    foreach ($images as $img) {
                        $thumbs .= '<img src="' . asset('storage/' . $img) . '" class="img-thumbnail me-1" style="max-width:48px;max-height:48px;">';
                    }
                }

                return [
                    'id' => $r->id,
                    'product' => $r->product->name ?? ('#' . $r->product_id),
                    'user' => $r->user->username ?? '',
                    'rating' => $stars,
                    'title' => e($r->title ?? ''),
                    'comment' => e(Str::limit($r->comment ?? '', 140)),
                    'images' => $thumbs ?: '-',
                    'status' => $select,
                    'created_at' => $r->created_at ? $r->created_at->format('d-m-Y H:i') : '',
                    'operate' => $action,
                ];
            });

        return response()->json([
            'rows' => $reviews,
            'total' => $total,
        ]);
    }

    public function update_status($id, Request $request)
    {
        $cfg = $this->config($this->currentType());
        $model = $cfg['model'];

        $review = $model::findOrFail($id);

        $status = $request->status;
        if ($status === null || !array_key_exists((int) $status, $this->statuses)) {
            return response()->json(['status_error' => labels('admin_labels.invalid_status', 'Invalid status')]);
        }

        $review->status = (int) $status;
        $review->save();

        // Approving / un-approving changes which reviews count toward the
        // product's published average rating, so recompute aggregates.
        app($cfg['rating_controller'])->recalculateAggregates($review->product_id);

        return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
    }

    public function destroy($id)
    {
        $cfg = $this->config($this->currentType());
        $model = $cfg['model'];

        $review = $model::find($id);

        if (!$review) {
            return response()->json(['error' => labels('admin_labels.data_not_found', 'Data Not Found')]);
        }

        // Reuse the existing delete logic (removes stored images + recomputes aggregates).
        app($cfg['rating_controller'])->delete_rating($id);

        return response()->json([
            'error' => false,
            'message' => labels('admin_labels.review_deleted_successfully', 'Review deleted successfully!'),
        ]);
    }

    public function delete_selected_data(Request $request)
    {
        $cfg = $this->config($this->currentType());

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:' . $cfg['ids_table'] . ',id',
        ]);

        $ratingController = app($cfg['rating_controller']);
        foreach ($request->ids as $id) {
            $ratingController->delete_rating($id);
        }

        return response()->json([
            'error' => false,
            'message' => labels('admin_labels.review_deleted_successfully', 'Selected reviews deleted successfully!'),
        ]);
    }
}
