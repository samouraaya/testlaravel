<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
 /**
 * @OA\Post(
 *     path="/api/profiles/{profile}/comments",
 *     summary="Add a comment to a profile",
 *     description="Allows an authenticated administrator to post a comment on a profile",
 *     operationId="storeComment",
 *     tags={"Comments"},
 *     security={{"bearerAuth":{}}},
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
public function storeComment(StoreCommentRequest $request)
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
    /**
     * @OA\Post(
     *     path="/api/profiles",
     *     summary="Create a new profile",
     *     description="Creates a new profile with an image, last name, first name, and status. Only accessible to authenticated administrators.",
     *     operationId="storeProfile",
     *     tags={"Profiles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"last_name", "first_name", "status", "image"},
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="The last name of the profile"
     *                 ),
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="The first name of the profile"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     description="The status of the profile (active, inactive, pending)"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="file",
     *                     description="The profile image file"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profile created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="administrator_id", type="integer"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Missing or invalid parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Image file is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Authentication required",
     *     )
     * )
     */
    public function store(ProfileRequest $request)
    {
       
        $validatedData =(object)$request->validated();
    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/profiles', 'public');
        } else {
            return response()->json(['error' => 'Image file is required.'], 400);
        }
        
        $profile = Profile::create([
            'last_name' => $validatedData->last_name,
            'first_name' => $validatedData->first_name,
            'image' => $path,
            'status' => $validatedData->status,
            'administrator_id' => auth()->user()->id, 
        ]);

        return response()->json($profile, 201);
        
    }
    /**
     * @OA\Get(
     *     path="/api/getprofiles",
     *     summary="Get all active profiles",
     *     description="Retrieve all profiles with 'active' status. This endpoint is public and does not require authentication.",
     *     operationId="getActiveProfiles",
     *     tags={"Profiles"},
     *     @OA\Response(
     *         response=200,
     *         description="List of active profiles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="image", type="string", example="/storage/images/profiles/profile1.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $profiles = Profile::where('status', 'active')->get(['id', 'last_name', 'first_name', 'image']); // Exclude the field "statut"

        return response()->json($profiles);
    }
    /**
     * @OA\Get(
     *     path="/api/profiles",
     *     summary="Get all profiles",
     *     description="Retrieve all profiles including the 'status' field. This endpoint requires authentication.",
     *     operationId="getAllProfiles",
     *     tags={"Profiles"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="List of all profiles with status",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="image", type="string", example="/storage/images/profiles/profile1.jpg"),
     *                 @OA\Property(property="status", type="string", example="active")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function indexAll()
    {
        // Récupérer tous les profils avec le champ "statut"
        $profiles = Profile::all(); // Inclut tous les champs

        return response()->json($profiles);
    }
    /**
     * @OA\Post(
     *     path="/api/updateProfiles/{profile}",
     *     summary="Update a profile",
     *     description="Update a profile including replacing the image if a new one is uploaded. This endpoint requires authentication.",
     *     operationId="updateProfile",
     *     tags={"Profiles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="profile",
     *         in="path",
     *         description="ID of the profile to update",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="image", type="string", format="binary", description="New profile image (optional)"),
     *                 @OA\Property(property="status", type="string", example="active"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="profile", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="image", type="string", example="/storage/profiles/profile1.jpg"),
     *                 @OA\Property(property="status", type="string", example="active")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Image file is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update(ProfileRequest $request, Profile $profile)
    { 
            // Vérifiez si une nouvelle image a été soumise
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($profile->image) {
                Storage::disk('public')->delete($profile->image);
            }

            // Stocker la nouvelle image
            $imagePath = $request->file('image')->store('profiles', 'public');
            $profile->image = $imagePath;
        }

        // Mettre à jour les autres champs
        $profile->update($request->except('image')); // Exclure 'image' pour ne pas écraser le chemin déjà mis à jour

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }
    /**
     * @OA\Delete(
     *     path="/api/deleteProfiles/{profile}",
     *     summary="Delete a profile",
     *     description="Delete a profile by its ID. This endpoint requires authentication and is accessible only to admins.",
     *     operationId="deleteProfile",
     *     tags={"Profiles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="profile",
     *         in="path",
     *         description="ID of the profile to delete",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profile deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function destroy(Profile $profile)
    {
        // Suppression du profil
        $profile->delete();

        return response()->json(['message' => 'Profile deleted successfully']);
    }

}
