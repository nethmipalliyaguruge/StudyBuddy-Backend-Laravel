<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialResource;
use App\Models\Note;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MaterialController extends Controller
{
    /**
     * List all approved materials with filtering and pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Note::with(['module.level.school', 'user', 'media'])
            ->where('status', 'approved');

        // Filter by school
        if ($request->filled('school_id')) {
            $query->whereHas('module.level', function ($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }

        // Filter by level
        if ($request->filled('level_id')) {
            $query->whereHas('module', function ($q) use ($request) {
                $q->where('level_id', $request->level_id);
            });
        }

        // Filter by module
        if ($request->filled('module_id')) {
            $query->where('module_id', $request->module_id);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortField = 'created_at';
        $sortDirection = 'desc';

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $sortDirection = 'asc';
                    break;
                case 'price_low':
                    $sortField = 'price';
                    $sortDirection = 'asc';
                    break;
                case 'price_high':
                    $sortField = 'price';
                    $sortDirection = 'desc';
                    break;
                case 'newest':
                default:
                    $sortField = 'created_at';
                    $sortDirection = 'desc';
                    break;
            }
        }

        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = min($request->input('per_page', 15), 50);
        $materials = $query->paginate($perPage);

        return MaterialResource::collection($materials);
    }

    /**
     * Get a single material by ID.
     */
    public function show(Note $note): JsonResponse
    {
        // Only show approved materials
        if ($note->status !== 'approved') {
            abort(404, 'Material not found');
        }

        $note->load(['module.level.school', 'user', 'media']);

        return response()->json([
            'data' => new MaterialResource($note),
        ]);
    }

    /**
     * Download a purchased note file.
     *
     * This endpoint is public (no auth:sanctum middleware) because it is opened
     * in an external browser via url_launcher, which cannot send Bearer headers.
     * Authentication is performed manually via the ?token= query parameter.
     */
    public function download(Request $request, Note $note): BinaryFileResponse|JsonResponse
    {
        // 1. Authenticate via query-param token
        $token = $request->query('token');

        if (!$token) {
            abort(401, 'Authentication token is required.');
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            abort(401, 'Invalid authentication token.');
        }

        $user = $accessToken->tokenable;

        // 2. Verify the user has a completed purchase for this note
        $purchase = Purchase::where('user_id', $user->id)
            ->where('note_id', $note->id)
            ->where('status', 'completed')
            ->first();

        if (!$purchase) {
            abort(403, 'You have not purchased this note.');
        }

        // 3. Get the note file via Spatie MediaLibrary
        $media = $note->getFirstMedia('note_file');

        if (!$media) {
            abort(404, 'File not found.');
        }

        // 4. Stream the file download
        return response()->download($media->getPath(), $media->file_name);
    }
}
