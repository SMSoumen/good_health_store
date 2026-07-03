<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sticker;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Traits\HandlesValidation;
use App\Services\StoreService;
use App\Services\MediaService;

class StickerController extends Controller
{
    use HandlesValidation;

    public function index()
    {
        return view('admin.pages.forms.stickers');
    }

    public function store(Request $request)
    {
        $rules = [
            'text' => 'required|string|max:191',
            'image' => 'required',
        ];

        if ($response = $this->HandlesValidation($request, $rules)) {
            return $response;
        }

        $sticker = new Sticker();
        $sticker->text = trim($request->text);
        $sticker->image = $request->image;
        $sticker->status = 1;
        $sticker->store_id = app(StoreService::class)->getStoreId();
        $sticker->save();

        if ($request->ajax()) {
            return response()->json(['message' => labels('admin_labels.sticker_created_successfully', 'Sticker created successfully')]);
        }

        return redirect()->back()->with('success', labels('admin_labels.sticker_created_successfully', 'Sticker created successfully'));
    }

    public function list(Request $request)
    {
        $storeId = app(StoreService::class)->getStoreId();
        $search = trim(request('search'));
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $offset = $search || request('pagination_offset') ? request('pagination_offset') : 0;
        $limit = request('limit', 10);
        $status = $request->input('status', '');

        $query = Sticker::where('store_id', $storeId)
            ->when($search, fn($q) => $q->where('text', 'LIKE', '%' . $search . '%'));

        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
        }

        $total = $query->count();

        $stickers = $query->orderBy($sort, $order)->offset($offset)->limit($limit)->get();

        $data = $stickers->map(function ($s) {
            $editUrl = route('stickers.edit', $s->id);
            $deleteUrl = route('stickers.destroy', $s->id);
            $action = '<div class="dropdown bootstrap-table-dropdown">
                    <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-horizontal-rounded"></i>
                    </a>
                    <div class="dropdown-menu table_dropdown" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item dropdown_menu_items" href="' . $editUrl . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                        <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $deleteUrl . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                    </div>
                </div>';
            $image = route('admin.dynamic_image', [
                'url' => app(MediaService::class)->getMediaImageUrl($s->image),
                'width' => 60,
                'quality' => 90,
            ]);

            return [
                'id' => $s->id,
                'text' => $s->text,
                'operate' => $action,
                'status' => '<select class="form-select change_toggle_status ' . ($s->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $s->id . '" data-url="/admin/sticker/update_status/' . $s->id . '" aria-label="">' .
                    '<option value="1" ' . ($s->status == 1 ? 'selected' : '') . '>Active</option>' .
                    '<option value="0" ' . ($s->status == 0 ? 'selected' : '') . '>Deactive</option>' .
                    '</select>',
                'image' => '<div class=""><a href="' . app(MediaService::class)->getMediaImageUrl($s->image) . '" data-lightbox="image-' . $s->id . '"><img src="' . $image . '" alt="Sticker" class="rounded"/></a></div>',
            ];
        });

        return response()->json([
            "rows" => $data,
            "total" => $total,
        ]);
    }

    public function update_status($id, Request $request)
    {
        $sticker = Sticker::findOrFail($id);

        $status = $request->status ?? null;
        if ($status === null) {
            return response()->json(['status_error' => 'Invalid status']);
        }

        if ($sticker->status != $status) {
            $sticker->status = $status;
            $sticker->save();
        }

        return response()->json([
            'success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')
        ]);
    }

    public function edit($id)
    {
        $storeId = app(StoreService::class)->getStoreId();
        $data = Sticker::where('store_id', $storeId)->find($id);

        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        }

        return view('admin.pages.forms.update_sticker', ['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'text' => 'required|string|max:191',
            'image' => 'required',
        ];

        if ($response = $this->HandlesValidation($request, $rules)) {
            return $response;
        }

        $sticker = Sticker::find($id);
        if (!$sticker) {
            return response()->json(['error' => 'Sticker not found.'], 404);
        }

        $sticker->text = trim($request->text);
        $sticker->image = $request->image;
        $sticker->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.sticker_updated_successfully', 'Sticker updated successfully'),
                'location' => route('stickers.index')
            ]);
        }

        return redirect()->route('stickers.index')->with('success', labels('admin_labels.sticker_updated_successfully', 'Sticker updated successfully'));
    }

    public function destroy($id)
    {
        $sticker = Sticker::find($id);
        if ($sticker) {
            $sticker->products()->detach();
            $sticker->comboProducts()->detach();
            $sticker->delete();
            return response()->json(['error' => false, 'message' => labels('admin_labels.sticker_deleted_successfully', 'Sticker deleted successfully')]);
        }

        return response()->json(['error' => labels('admin_labels.data_not_found', 'Data Not Found')]);
    }

    public function delete_selected_data(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:stickers,id'
        ]);

        foreach ($request->ids as $id) {
            $sticker = Sticker::find($id);
            if ($sticker) {
                $sticker->products()->detach();
                $sticker->comboProducts()->detach();
                $sticker->delete();
            }
        }

        return response()->json(['message' => 'Selected stickers deleted successfully.']);
    }
}
