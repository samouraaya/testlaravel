<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/profiles/{profile}/comments",
 *     summary="Add a comment to a profile",
 *     description="Allows an authenticated administrator to post a comment on a profile",
 *     operationId="storeComment",
 *     tags={"Comments"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="profile",
 *         in="path",
 *         required=true,
 *         description="ID of the profile",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content", "profile_id"},
 *             @OA\Property(property="content", type="string", example="This is a comment"),
 *             @OA\Property(property="profile_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Comment added successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="You have already commented on this profile"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    public function store(StoreCommentRequest $request)
    { 
        $adminId = auth()->user()->id;
        $profileId = $request->input('profile_id');

        $existingComment = Comment::where('administrator_id', $adminId)
                                ->where('profile_id', $profileId)
                                ->first();

        if ($existingComment) {
            return response()->json(['message' => 'You have already commented on this profile'], 400);
        }

        // Proceed to create the comment
        $comment = Comment::create([
            'content' => $request->input('content'),
            'administrator_id' => $adminId,
            'profile_id' => $profileId,
        ]);

        return response()->json($comment, 201);
    }

}
