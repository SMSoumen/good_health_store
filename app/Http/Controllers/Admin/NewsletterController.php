<?php

namespace App\Http\Controllers\Admin;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Traits\HandlesValidation;

class NewsletterController extends Controller
{
    use HandlesValidation;

    public function index()
    {
        return view('admin.pages.forms.newsletter_subscribers');
    }

    public function store(Request $request)
    {
        $rules = [
            'email' => 'required|email|max:191|unique:newsletter_subscribers,email',
        ];

        if ($response = $this->HandlesValidation($request, $rules)) {
            return $response;
        }

        NewsletterSubscriber::create([
            'email' => strtolower(trim($request->email)),
            'status' => 1,
            'store_id' => session('store_id'),
            'ip_address' => $request->ip(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.subscriber_added_successfully', 'Subscriber added successfully'),
            ]);
        }
    }

    public function list()
    {
        $search = trim(request('search'));
        $sort = request('sort') ?: 'id';
        $order = request('order') ?: 'DESC';
        $offset = $search || request('pagination_offset') ? request('pagination_offset') : 0;
        $limit = request('limit') ?: '10';

        $subscribers = NewsletterSubscriber::when($search, function ($query) use ($search) {
            return $query->where('email', 'like', '%' . $search . '%');
        });

        $total = $subscribers->count();

        $subscribers = $subscribers->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($s) {
                $delete_url = route('admin.newsletter.destroy', $s->id);
                $action = '<div class="dropdown bootstrap-table-dropdown">
                <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                </div>
            </div>';

                $status = '<select class="form-select change_toggle_status ' . ($s->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $s->id . '" data-url="' . route('admin.newsletter.update_status', $s->id) . '" aria-label="">' .
                    '<option value="1" ' . ($s->status == 1 ? 'selected' : '') . '>' . labels('admin_labels.subscribed', 'Subscribed') . '</option>' .
                    '<option value="0" ' . ($s->status == 0 ? 'selected' : '') . '>' . labels('admin_labels.unsubscribed', 'Unsubscribed') . '</option>' .
                    '</select>';

                return [
                    'id' => $s->id,
                    'email' => $s->email,
                    'status' => $status,
                    'created_at' => $s->created_at ? $s->created_at->format('d-m-Y H:i') : '',
                    'operate' => $action,
                ];
            });

        return response()->json([
            'rows' => $subscribers,
            'total' => $total,
        ]);
    }

    public function update_status($id, Request $request)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);

        $status = $request->status;
        if ($status === null) {
            return response()->json(['status_error' => labels('admin_labels.invalid_status', 'Invalid status')]);
        }

        $subscriber->status = $status;
        $subscriber->save();

        return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
    }

    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if ($subscriber) {
            $subscriber->delete();
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.subscriber_deleted_successfully', 'Subscriber deleted successfully!'),
            ]);
        }

        return response()->json(['error' => labels('admin_labels.data_not_found', 'Data Not Found')]);
    }

    public function delete_selected_data(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:newsletter_subscribers,id',
        ]);

        NewsletterSubscriber::whereIn('id', $request->ids)->delete();

        return response()->json([
            'error' => false,
            'message' => labels('admin_labels.subscriber_deleted_successfully', 'Selected subscribers deleted successfully!'),
        ]);
    }
}
