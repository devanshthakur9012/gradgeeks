<?php

namespace Chatify\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use App\Models\ChMessage as Message;
use App\Models\ChFavorite as Favorite;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class MessagesController extends Controller
{

    public function pusherAuth(Request $request)
    {
        // Auth data
        $authData = json_encode([
            'user_id' => Auth::user()->id,
            'user_info' => [
                'name' => Auth::user()->name
            ]
        ]);
        // check if user authorized
        if (Auth::check()) {
            return Chatify::pusherAuth(
                $request['channel_name'],
                $request['socket_id'],
                $authData
            );
        }
        // if not authorized
        return new Response('Unauthorized', 401);
    }


    public function index($id = null)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug('');

        if (Auth::user()->type != 'admin') {

            $routeName = FacadesRequest::route()->getName();
            $route = (in_array($routeName, ['user', config('chatify.routes.prefix')]))
                ? 'user'
                : $routeName;

            // prepare id
            return view('Chatify::pages.app', [
                'id' => ($id == null) ? 0 : $route . '_' . $id,
                'route' => $route,
                'messengerColor' => Auth::user()->messenger_color,
                'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
                'currentWorkspace' => $currentWorkspace,
            ]);
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }



    public function idFetchData(Request $request)
    {
        // Favorite
        $favorite = Chatify::inFavorite($request['id']);

        $profile_img  = Utility::get_file(config('chatify.user_avatar.folder'));

        // User data
        if ($request['type'] == 'user') {
            $fetch = User::where('id', $request['id'])->first();
        }

        if (!empty($fetch->avatar)) {

            $avatar = Utility::get_file('/' . config('chatify.user_avatar.folder') . '/' . $fetch->avatar);
            // $avatar = url('/').$avatar;
        } else {

            $avatar = Utility::get_file('/' . config('chatify.user_avatar.folder') . '/avatar.png');

            //  $avatar = url('/').$avatar;
        }

        // send the response
        return Response::json([
            'favorite' => $favorite,
            'fetch' => $fetch ?? [],
            'user_avatar' => $avatar,
        ]);
    }


    public function download($fileName)
    {
        $logo_img  = Utility::get_file('/app/public/');
        $profile_img  = Utility::get_file(config('chatify.attachments.folder'));
        $settings = Utility::getAdminPaymentSettings();
        if ($settings['storage_setting'] == 'local') {
            // $path = storage_path() . '/app/public/' . config('chatify.attachments.folder') . '/' . $fileName;
            $path = storage_path() . '/' . config('chatify.attachments.folder') . '/' . $fileName;
        } else {
            $path = $logo_img . $profile_img . '/' . $fileName;
        }

        if (file_exists($path)) {
            return Response::download($path, $fileName);
        } else {
            return abort(404, "Sorry, File does not exist in our server or may have been deleted!");
        }
    }



    public function send(Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files  = Chatify::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');

            if (in_array($file->getClientOriginalExtension(), $allowed)) {
                // get attachment name
                $attachment_title = $file->getClientOriginalName();
                // upload attachment and store the new name
                $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();

                // $file->storeAs(config('chatify.attachments.folder'), $attachment);

                $dir = 'attachments/';

                $path_store = Utility::upload_file($request, 'file', $attachment, $dir, []);
                if ($path_store['flag'] == 1) {
                    $file = $path_store['url'];
                } else {

                    return redirect()->back()->with('error', __($path_store['msg']));
                }
            } else {
                $error->status = 1;
                $error->message = "File extension not allowed!";
            }
        }

        if (!$error->status) {
            // send to database
            $messageID = mt_rand(9, 999999999) + time();
            Chatify::newMessage([
                'id' => $messageID,
                'type' => $request['type'],
                'workspace_id' => $currentWorkspace->id,
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object)[
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);

            // fetch message to send it with the response
            $messageData = Chatify::fetchMessage($messageID);

            // send to user using pusher
            Chatify::push('private-chatify', 'messaging', [
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'message' => Chatify::messageCard($messageData, 'default')
            ]);
        }

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => Chatify::messageCard(@$messageData),
            'tempID' => $request['temporaryMsgId'],
        ]);
    }

    public function fetch(Request $request)
    {
        // messages variable
        $allMessages = null;

        // fetch messages
        $query = Chatify::fetchMessagesQuery($request['id'])->orderBy('created_at', 'asc');
        $messages = $query->get();

        // if there is a messages
        if ($query->count() > 0) {
            foreach ($messages as $message) {
                $allMessages .= Chatify::messageCard(
                    Chatify::fetchMessage($message->id)
                );
            }
            // send the response
            return Response::json([
                'count' => $query->count(),
                'messages' => $allMessages,
            ]);
        }
        // send the response
        return Response::json([
            'count' => $query->count(),
            'messages' => '<p class="message-hint center-el"><span>Say \'hi\' and start messaging</span></p>',
        ]);
    }


    public function seen(Request $request)
    {
        // make as seen
        $seenmessage =  Message::Where('from_id', $request['id'])->where('to_id', Auth::user()->id)->where('seen', 0)->count();
        $messageCount = Message::where('to_id', Auth::user()->id)->where('seen', 0)->count();

        $seen = Chatify::makeSeen($request['id']);

        if ($seen) {
            $messageCount = $messageCount - $seenmessage;
        }
        // send the response
        return Response::json(
            [
                'status' => $seen,
                'messengerCount' => $messageCount
            ],
            200
        );
    }

    public function getContacts(Request $request)
    {
        // get all users that received/sent message from/to [Auth user]
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        $users = Message::join('users',  function ($join) {
            $join->on('ch_messages.from_id', '=', 'users.id')
                ->orOn('ch_messages.to_id', '=', 'users.id');
        })
            ->where('ch_messages.workspace_id', '=', $currentWorkspace->id)
            ->where(function ($q) {
                $q->where('ch_messages.from_id', Auth::user()->id)
                    ->orWhere('ch_messages.to_id', Auth::user()->id);
            })
            ->orderBy('ch_messages.created_at', 'desc')
            ->get()
            ->unique('id');


        // dd($currentWorkspace);
        $users = $users->where('id', '!=', Auth::user()->id);
        if ($users->count() > 0) {
            // fetch contacts
            $contacts = '';
            foreach ($users as $user) {
                if ($user->id != Auth::user()->id) {
                    // Get user data
                    $userCollection = User::where('id', $user->id)->first();
                    $contacts .= Chatify::getContactItem($request['messenger_id'], $userCollection);
                }
            }
        }

        $objUser = Auth::user();

        if ($currentWorkspace) {
            $members = User::select('users.*', 'user_workspaces.permission', 'user_workspaces.is_active')
                ->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')
                ->where('user_workspaces.workspace_id', '=', $currentWorkspace->id)->where('users.id', '!=', $objUser->id)->get();
        } else {
            $members = User::where('type', '!=', 'admin')->get();
        }

        $getRecords = null;
        foreach ($members as $record) {

            $getRecords .= view(
                'vendor.Chatify.layouts.listItem',
                [
                    'get' => 'all_members',
                    'type' => 'user',
                    'user' => $record,
                ]
            )->render();
        }

        return Response::json(
            [
                'contacts' => $users->count() > 0 ? $contacts : '<br><p class="message-hint"><span>' . __('Your contact list is empty') . '</span></p>',
                'allUsers' => $members->count() > 0 ? $getRecords : '<br><p class="message-hint"><span>' . __('Your member list is empty') . '</span></p>',
            ],
            200
        );
    }


    public function updateContactItem(Request $request)
    {
        // Get user data
        $userCollection = User::where('id', $request['user_id'])->first();
        $contactItem    = Chatify::getContactItem($request['messenger_id'], $userCollection);
        $messageCount = Message::where('to_id', Auth::user()->id)->where('seen', 0)->count();
        // send the response
        return Response::json(
            [
                'contactItem' => $contactItem,
                'messengerCount' => $messageCount
            ],
            200
        );
    }

    /**
     * Put a user in the favorites list
     *
     * @param Request $request
     * @return void
     */
    public function favorite(Request $request)
    {
        // check action [star/unstar]
        if (Chatify::inFavorite($request['user_id'])) {
            // UnStar
            Chatify::makeInFavorite($request['user_id'], 0);
            $status = 0;
        } else {
            // Star
            Chatify::makeInFavorite($request['user_id'], 1);
            $status = 1;
        }

        // send the response
        return Response::json([
            'status' => @$status,
        ], 200);
    }

    /**
     * Get favorites list
     *
     * @param Request $request
     * @return void
     */
    public function getFavorites(Request $request)
    {
        $favoritesList = null;
        $favorites = Favorite::where('user_id', Auth::user()->id);
        foreach ($favorites->get() as $favorite) {
            // get user data
            $user = User::where('id', $favorite->favorite_id)->first();
            $favoritesList .= view('Chatify::layouts.favorite', [
                'user' => $user,
            ]);
        }
        // send the response
        return Response::json([
            'count' => $favorites->count(),
            'favorites' => $favorites->count() > 0
                ? $favoritesList
                : '<p class="message-hint"><span>Your favourite list is empty</span></p>',
        ], 200);
    }

    /**
     * Search in messenger
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $getRecords = null;
        $input = trim(filter_var($request['input'], FILTER_SANITIZE_STRING));
        //$records = User::where('name', 'LIKE', "%{$input}%");
        $objUser          = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        if ($currentWorkspace) {
            $users = User::select('users.*', 'user_workspaces.permission', 'user_workspaces.is_active')
                ->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')
                ->where('user_workspaces.workspace_id', '=', $currentWorkspace->id)
                ->where('users.id', '!=', $objUser->id);
        } else {
            // $records = $users->where('name', 'LIKE', "%{$input}%")->get();
        }

        $records = User::where('name', 'LIKE', "%{$input}%");

        foreach ($records->get() as $record) {
            $getRecords .= view('Chatify::layouts.listItem', [
                'get' => 'search_item',
                'type' => 'user',
                'user' => $record,
            ])->render();
        }
        // send the response
        return Response::json([
            'records' => $records->count() > 0
                ? $getRecords
                : '<p class="message-hint center-el"><span>Nothing to show.</span></p>',
            'addData' => 'html'
        ], 200);
    }
    /**
     * Get shared photos
     *
     * @param Request $request
     * @return void
     */
    public function sharedPhotos(Request $request)
    {
        $shared = Chatify::getSharedPhotos($request['user_id']);
        $sharedPhotos = null;

        // shared with its template
        for ($i = 0; $i < count($shared); $i++) {
            $sharedPhotos .= view('Chatify::layouts.listItem', [
                'get' => 'sharedPhoto',
                'image' => asset('storage/attachments/' . $shared[$i]),
            ])->render();
        }
        // send the response
        return Response::json([
            'shared' => count($shared) > 0 ? $sharedPhotos : '<p class="message-hint"><span>Nothing shared yet</span></p>',
        ], 200);
    }


    public function deleteConversation(Request $request)
    {
        // delete
        $delete = Chatify::deleteConversation($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ], 200);
    }

    public function updateSettings(Request $request)
    {
        $msg = null;
        $error = $success = 0;

        $profile_img  = Utility::get_file(config('chatify.user_avatar.folder'));
        $file_path   = Utility::get_file('app/public/');
        $file_path2   = Utility::get_file('/public/');
        $settings = Utility::getAdminPaymentSetting();
        // dark mode
        if ($request['dark_mode']) {
            $request['dark_mode'] == "dark"
                ? User::where('id', Auth::user()->id)->update(['dark_mode' => 1])  // Make Dark
                : User::where('id', Auth::user()->id)->update(['dark_mode' => 0]); // Make Light
        }

        // If messenger color selected
        if ($request['messengerColor']) {

            $messenger_color = explode('-', trim(filter_var($request['messengerColor'], FILTER_SANITIZE_STRING)));
            $messenger_color = Chatify::getMessengerColors()[$messenger_color[1]];
            User::where('id', Auth::user()->id)
                ->update(['messenger_color' => $messenger_color]);
        }
        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();

            $file = $request->file('avatar');
            // if size less than 150MB
            // if ($file->getSize() < 150000000) {
            if (in_array($file->getClientOriginalExtension(), $allowed_images)) {
                // delete the older one
                if (Auth::user()->avatar != config('chatify.user_avatar.default')) {
                    //$path = storage_path('app/public/' . config('chatify.user_avatar.folder') . '/' . Auth::user()->avatar);


                    if ($settings['storage_setting'] == 'local') {
                        $path = storage_path('app/public/' . config('chatify.user_avatar.folder') . '/' . Auth::user()->avatar);
                    } else {
                        $path = $file_path . $profile_img . '/' . Auth::user()->avatar;
                    }




                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
                // upload
                $avatar = Str::uuid() . "." . $file->getClientOriginalExtension();
                $update = User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);

                if ($settings['storage_setting'] == 'local') {
                    $file->storeAs("public/" . config('chatify.user_avatar.folder'), $avatar);
                } else {
                    $dir = 'public/' . config('chatify.user_avatar.folder') . '/';
                    $path_store = Utility::upload_file($request, 'avatar', $avatar, $dir, []);
                    if ($path_store['flag'] == 1) {
                        $avatar_1 = $path_store['url'];
                    } else {
                        return redirect()->back()->with('error', __($path_store['msg']));
                    }
                }

                $success = $update ? 1 : 0;
            } else {
                $msg = "File extension not allowed!";
                $error = 1;
            }
            // } else {
            //     $msg = "File extension not allowed!";
            //     $error = 1;
            // }
        }

        // send the response
        return Response::json([
            'status' => $success ? 1 : 0,
            'error' => $error ? 1 : 0,
            'message' => $error ? $msg : 0,
        ], 200);
    }



    public function getMessagePopup()
    {
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        $user             = Auth::user();
        $messages         = Message::whereIn(
            'id',
            function ($query) use ($currentWorkspace, $user) {
                $query->select(DB::raw('MAX(id)'))->from('ch_messages')->where('workspace_id', '=', $currentWorkspace->id)->where('to_id', $user->id)->where('seen', '=', 0);
            }
        )->orderBy('id', 'desc')->get();

        return view('vendor.popup', compact('messages', 'currentWorkspace'));
    }



    public function messageSeen()
    {
        $currentWorkspace = Utility::getWorkspaceBySlug('');
        $user             = Auth::user();
        Message::where('workspace_id', '=', $currentWorkspace->id)->where('to_id', '=', $user->id)->update(['seen' => 1]);

        return response()->json(['is_success' => true], 200);
    }




    public function setActiveStatus(Request $request)
    {
        $update = $request['status'] > 0
            ? User::where('id', $request['user_id'])->update(['active_status' => 1])
            : User::where('id', $request['user_id'])->update(['active_status' => 0]);
        // send the response
        return Response::json([
            'status' => $update,
        ], 200);
    }
}
