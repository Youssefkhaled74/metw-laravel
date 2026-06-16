<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationAsReadRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $users = User::all();
            return responseJson(true,'Users fetched successfully',UserResource::collection($users));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $user = User::findOrFail($id);
            return responseJson(true,'User fetched successfully',$user);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $user = User::findOrFail($request->user()->id);
            if ($request->hasFile('image')) {
                $upload = uploadImage($request, 'image', 'storage/users');
                if ($upload) {
                    deleteImage($user->image ?? null);
                    $validatedData['image'] = is_array($upload) ? $upload['path'] : $upload;
                }
            }
            if(isset($validatedData['image_remove']) && $validatedData['image_remove']){
                deleteImage($user->image ?? null);
                $validatedData['image'] = null;
            }
            $user->update($validatedData);
            return responseJson(true,'User updated successfully',['user' => $user]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
    public function toggleNotifications(Request $request)
    {
        try{
            $user = User::findOrFail(auth()->user()->id);
            $toggle = $user->notifications_enabled ? false : true;
            $user->update(['notifications_enabled' => $toggle]);
            return responseJson(true,'User notifications updated successfully',['user' => $user]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
    public function toggleNotificationsshipment(Request $request)
    {
        try{
            $user = User::findOrFail(auth()->user()->id);
            $toggle = $user->enable_shipment_notifications ? false : true;
            $user->update(['enable_shipment_notifications' => $toggle]);
            return responseJson(true,'User notifications updated successfully',['user' => $user]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try{
            $user = User::findOrFail($request->user()->id);
            $user->delete();
            return responseJson(true,'User deleted successfully',$user);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    public function getNotifications(Request $request)
    {
        try {
            $user = auth()->user();

            // فلترة الإشعارات بحيث notification_type = shipment
            $notificationsQuery = $user->notifications()
                ->whereIn('data->notification_type', ['shipment', 'custom'])
                ->latest();

            // تطبيق الـ paginate helper
            $payload = paginate(
                $notificationsQuery,
                NotificationResource::class,
                $request->get('limit', 10),   // limit
                $request->get('page', 1),     // page number
                $request->all()             
            );

            return responseJson(true, 'User shipment notifications fetched successfully', $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }



    public function markAsRead(NotificationAsReadRequest $request)
    {
        try{
            $validatedData = $request->validated();

            $notificationId = $validatedData['notification_id'];

            $user = User::findOrFail(auth()->user()->id);

            $notification = $user->notifications()->findOrFail($notificationId);

            if($notification->read_at){
                return responseJson(false,'Notification already read',null,422);
            }

            $notification->markAsRead();
            return responseJson(true,'User notification marked as read successfully',$notification);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    public function getNotificationsecommerce(Request $request)
    {
        try {
            $user = auth()->user();

            // Query notifications for ecommerce
            $notificationsQuery = $user->notifications()
                ->whereIn('data->notification_type', ['ecommerce', 'custom'])
                ->latest();

            // Apply pagination (once)
            $payload = paginate(
                $notificationsQuery,
                NotificationResource::class,
                $request->get('limit', 10),
                $request->get('page', 1),
                $request->all()
            );

            // Extract the collection from pagination data
            $notifications = collect($payload['data'] ?? []);

            // Group inside the same page
            $grouped = [
                'today' => $notifications->filter(fn($n) =>
                    \Carbon\Carbon::parse($n['created_at'])->isToday()
                )->values(),
                'others' => $notifications->filter(fn($n) =>
                    !\Carbon\Carbon::parse($n['created_at'])->isToday()
                )->values(),
            ];

            // Replace 'data' in payload with grouped results
            $payload['data'] = $grouped;

            return responseJson(true, 'User ecommerce notifications fetched successfully', $payload);

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function setDefaultLang(Request $request)
    {
        try {
            $request->validate([
                'default_lang' => 'required_without:default_shipment_lang|string|in:en,ar',
                'default_shipment_lang' => 'required_without:default_lang|string|in:en,ar',
            ]);

            $user = auth()->user();

            if ($request->filled('default_lang')) {
                $user->default_lang = $request->input('default_lang');
            }

            if ($request->filled('default_shipment_lang')) {
                $user->default_shipment_lang = $request->input('default_shipment_lang');
            }

            $user->save();

            return responseJson(true, 'Default language updated successfully', ['user' => $user]);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
