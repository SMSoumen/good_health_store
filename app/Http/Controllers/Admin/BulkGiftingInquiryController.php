<?php

namespace App\Http\Controllers\Admin;

use App\Models\BulkGiftingInquiry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkGiftingInquiryController extends Controller
{
    /**
     * Requirement-type keys (stored on the inquiry) mapped to display labels.
     */
    private function requirementLabels(): array
    {
        return [
            'corporate' => labels('admin_labels.bulk_req_corporate', 'Corporate Gifting'),
            'wedding'   => labels('admin_labels.bulk_req_wedding', 'Wedding Gifting'),
            'festive'   => labels('admin_labels.bulk_req_festive', 'Festive Hampers'),
            'reselling' => labels('admin_labels.bulk_req_reselling', 'Reselling'),
            'bulk'      => labels('admin_labels.bulk_req_bulk', 'Bulk Purchase'),
            'other'     => labels('admin_labels.bulk_req_other', 'Other'),
        ];
    }

    private function formatTypes($types): string
    {
        $labels = $this->requirementLabels();
        $types = is_array($types) ? $types : [];
        $mapped = array_map(fn ($key) => $labels[$key] ?? $key, $types);

        return $mapped ? implode(', ', $mapped) : '-';
    }

    public function index()
    {
        return view('admin.pages.forms.bulk_gifting_inquiries');
    }

    /**
     * Apply the shared search filter (name / phone / email / company).
     */
    private function applySearch($query, ?string $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('company', 'like', '%' . $search . '%');
            });
        });
    }

    public function list()
    {
        $search = trim(request('search'));
        $sort = request('sort') ?: 'id';
        $order = request('order') ?: 'DESC';
        $offset = $search || request('pagination_offset') ? request('pagination_offset') : 0;
        $limit = request('limit') ?: '10';

        $query = $this->applySearch(BulkGiftingInquiry::query(), $search);

        $total = $query->count();

        $rows = $query->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($i) {
                $delete_url = route('admin.bulk_gifting_inquiries.destroy', $i->id);
                $action = '<div class="dropdown bootstrap-table-dropdown">
                <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                </div>
            </div>';

                $status = '<select class="form-select change_toggle_status ' . ($i->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $i->id . '" data-url="' . route('admin.bulk_gifting_inquiries.update_status', $i->id) . '" aria-label="">' .
                    '<option value="0" ' . ($i->status == 0 ? 'selected' : '') . '>' . labels('admin_labels.new', 'New') . '</option>' .
                    '<option value="1" ' . ($i->status == 1 ? 'selected' : '') . '>' . labels('admin_labels.contacted', 'Contacted') . '</option>' .
                    '</select>';

                return [
                    'id' => $i->id,
                    'name' => e($i->name),
                    'phone' => e($i->phone),
                    'email' => e($i->email),
                    'company' => e($i->company ?: '-'),
                    'requirement_types' => e($this->formatTypes($i->requirement_types)),
                    'estimated_quantity' => e($i->estimated_quantity ?: '-'),
                    'message' => $i->message ? nl2br(e($i->message)) : '-',
                    'status' => $status,
                    'created_at' => $i->created_at ? $i->created_at->format('d-m-Y H:i') : '',
                    'operate' => $action,
                ];
            });

        return response()->json([
            'rows' => $rows,
            'total' => $total,
        ]);
    }

    public function update_status($id, Request $request)
    {
        $inquiry = BulkGiftingInquiry::findOrFail($id);

        $status = $request->status;
        if ($status === null) {
            return response()->json(['status_error' => labels('admin_labels.invalid_status', 'Invalid status')]);
        }

        $inquiry->status = $status;
        $inquiry->save();

        return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
    }

    public function destroy($id)
    {
        $inquiry = BulkGiftingInquiry::find($id);

        if ($inquiry) {
            $inquiry->delete();
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.inquiry_deleted_successfully', 'Inquiry deleted successfully!'),
            ]);
        }

        return response()->json(['error' => labels('admin_labels.data_not_found', 'Data Not Found')]);
    }

    public function delete_selected_data(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:bulk_gifting_inquiries,id',
        ]);

        BulkGiftingInquiry::whereIn('id', $request->ids)->delete();

        return response()->json([
            'error' => false,
            'message' => labels('admin_labels.inquiry_deleted_successfully', 'Selected inquiries deleted successfully!'),
        ]);
    }

    /**
     * Stream every inquiry (respecting the current search filter) as a CSV.
     * Unlike the client-side toolbar export, this covers the full result set,
     * not just the rows on the current page.
     */
    public function export(): StreamedResponse
    {
        $search = trim(request('search'));
        $query = $this->applySearch(BulkGiftingInquiry::query(), $search)->orderBy('id', 'DESC');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bulk-gifting-inquiries-' . date('Y-m-d') . '.csv"',
        ];

        $columns = [
            labels('admin_labels.id', 'ID'),
            labels('admin_labels.name', 'Name'),
            labels('admin_labels.phone', 'Phone'),
            labels('admin_labels.email', 'Email'),
            labels('admin_labels.company', 'Company'),
            labels('admin_labels.requirement_type', 'Requirement Type'),
            labels('admin_labels.estimated_quantity', 'Estimated Quantity'),
            labels('admin_labels.message', 'Message'),
            labels('admin_labels.status', 'Status'),
            labels('admin_labels.submitted_on', 'Submitted On'),
        ];

        return response()->stream(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM so Excel renders accented characters correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $columns);

            $query->chunk(500, function ($inquiries) use ($handle) {
                foreach ($inquiries as $i) {
                    fputcsv($handle, [
                        $i->id,
                        $i->name,
                        $i->phone,
                        $i->email,
                        $i->company ?: '',
                        $this->formatTypes($i->requirement_types),
                        $i->estimated_quantity ?: '',
                        $i->message ?: '',
                        $i->status == 1 ? labels('admin_labels.contacted', 'Contacted') : labels('admin_labels.new', 'New'),
                        $i->created_at ? $i->created_at->format('d-m-Y H:i') : '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
