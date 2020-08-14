<?php

namespace App\Http\Controllers\Api;

use App\DirectMessage;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RealtimeController extends Controller
{
    public function contacts (Request $request)
    {
        $skip = $request->get('skip', 0);
        $limit = $request->get('limit', 20);
        $search = $request->get('search', null);
        $contacts = User::where('id', '<>', auth()->user()->id)
            ->skip($limit*$skip)->limit($limit)
            ->when($search != null, function($q) use ($search){
                return $q->where(function($q) use ($search) {
                    return $q->orWhere('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%");
                });
            })
            ->get();

        return response()->json($contacts);
    }

    public function conversations (Request $request)
    {
        // $limit = $request->get('limit', null);
        // $skip = $request->get('skip', 0) * (int) $request->get('limit', 0);

        $user = auth()->user();
        $conversations = User::where('id', '<>', $user->id)
            // ->skip($skip)->limit($limit)
            ->hasConversation($user)
            ->get();

        $conversations = $conversations->map(function($item) use ($user) {
            $item->last_message = $item->getLastMessage($user);
            return $item;
        });

        return response()->json($conversations);
    }

    public function fetchConverseMessages ($id, Request $request)
    {
        $limit = $request->get('limit', 20);
        $contact = User::findOrfail($id);
        $messages = auth()->user()->getDirectMessages($contact)
            ->when($request->lastMessageAt, function($q) use ($request) {
                $from = now()->parse(request('lastMessageAt'))->addSeconds(-1);
                return $q->where('created_at', '<=', $from->toDateTimeLocalString());
            })
            ->limit($limit)
            ->latest()->get();

        return response()->json($messages);
    }

    public function sendMessage (Request $request)
    {
        $request->validate([
            'sender_id' => 'in:'. auth()->user()->id,
            'receiver_id' => 'required',
            'text' => 'required',
        ]);

        User::findOrfail($request->receiver_id);

        $message = DirectMessage::create(array_merge($request->input()));

        event(new \App\Events\MessageableCreated($message));

        return response()->json($message);
    }

    public function receiveMessage ($id)
    {
        $message = DirectMessage::findOrfail($id);

        $received = DirectMessage::whereNull('received_at')
            ->where('receiver_id', auth()->user()->id)
            ->where('sender_id', $message->sender_id)
            ->update(['received_at' => now()]);

        // event(new \App\Events\MessageableCreated($message));

        return response()->json($message->fresh());
    }

    public function readMessage ($id)
    {
        $message = DirectMessage::findOrfail($id);

        $readed = DirectMessage::whereNull('readed_at')
            ->where('receiver_id', auth()->user()->id)
            ->where('sender_id', $message->sender_id)
            ->update(['readed_at' => now()]);

        // event(new \App\Events\MessageableCreated($message));

        return response()->json($message->fresh());
    }
}
