<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\User;
use Livewire\Component;

class Users extends Component
{
    public function message($userId){
        $authUserId = auth()->id();
        #check conversation exists
        $existingConv = Conversation::where(function ($query) use ($authUserId, $userId){
            $query->where("sender_id", $userId)
            ->where('receiver_id', $authUserId);
        })->orWhere(function($query) use ($authUserId, $userId){
            $query->where("sender_id", $authUserId)
            ->where('receiver_id', $userId);
        })->first();
        if($existingConv){
            return redirect()->route('chat',['query' => $existingConv->id]);
        }
        #create conversation
        $createdConv = Conversation::create([
            'sender_id' => $authUserId,
            'receiver_id' => $userId
        ]);
        return redirect()->route('chat',['query' => $createdConv->id]);
    }
    public function render()
    {
        return view('livewire.users',['users' => User::where('id','!=',auth()->id())->get()]);
    }
}
