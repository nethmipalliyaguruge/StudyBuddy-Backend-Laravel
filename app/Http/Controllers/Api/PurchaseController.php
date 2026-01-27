<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * List authenticated user's purchases.
     */
    public function index(): AnonymousResourceCollection
    {
        $purchases = Purchase::with(['note.module.level.school', 'note.media', 'note.user'])
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->paginate(15);

        return PurchaseResource::collection($purchases);
    }

    /**
     * Get a single purchase with download access.
     */
    public function show(Purchase $purchase)
    {
        // Ensure user owns this purchase
        if ($purchase->user_id !== Auth::id()) {
            abort(403, 'You can only view your own purchases.');
        }

        if ($purchase->status !== 'completed') {
            abort(403, 'This purchase is not completed.');
        }

        $purchase->load(['note.module.level.school', 'note.media', 'note.user']);

        return response()->json([
            'data' => new PurchaseResource($purchase),
        ]);
    }

    /**
     * Get download URL for a purchased note.
     */
    public function download(Purchase $purchase)
    {
        // Ensure user owns this purchase
        if ($purchase->user_id !== Auth::id()) {
            abort(403, 'You can only download your own purchases.');
        }

        if ($purchase->status !== 'completed') {
            abort(403, 'This purchase is not completed.');
        }

        $note = $purchase->note;
        $media = $note->getFirstMedia('note_file');

        if (!$media) {
            abort(404, 'File not found.');
        }

        // Check if the disk supports temporary URLs (S3, etc.)
        $disk = $media->disk;
        $diskConfig = config("filesystems.disks.{$disk}");

        if (isset($diskConfig['driver']) && $diskConfig['driver'] === 's3') {
            // Cloud storage - use temporary URL
            return response()->json([
                'download_url' => $media->getTemporaryUrl(now()->addMinutes(30)),
                'filename' => $media->file_name,
                'expires_at' => now()->addMinutes(30)->toISOString(),
            ]);
        }

        // Local storage - return direct URL
        return response()->json([
            'download_url' => $media->getUrl(),
            'filename' => $media->file_name,
        ]);
    }
}
