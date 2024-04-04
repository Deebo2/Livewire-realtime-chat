<?php

namespace App\Livewire\Chat;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Message;

use Livewire\Attributes\On;
use Livewire\Component;

class ChatBox extends Component
{
    public $selectedConversation;
    public $body;
    public $loadedMessages;
    public $createdMessage;
    public $paginate_var = 10;
    #########################################
  
    ########################################
    public function getListeners(){
        $author_id = auth()->id();
        return [
            'loadMore','dispatchMessageSent','dispatchMessageRead',
            "echo-private:users.{$author_id},MessageSent" => 'broadcastedMessageSent',
        ];
    }
    #########################################
    public function broadcastedMessageSent($event){
        //dd($event);
       
            if($event['conversation_id'] == $this->selectedConversation->id){
                $this->dispatch('scroll-bottom');
                $newMessage = Message::find($event['message_id']);
                $this->loadedMessages->push($newMessage);
                #mark as read
                $newMessage->read_at = now();
                $newMessage->save();
                #broadcast
                $this->dispatch('dispatchMessageRead');
                
            
        }
    }
 
    #########################################
    public function loadMore(){
        #increment loaded messages count
        $this->paginate_var += 10;
        $this->loadMessages();
        #update the height
        $this->dispatch('update-chat-height');
    }
    #########################################
    public function loadMessages(){
        $user_id = auth()->id();
        #get messages count
        $count = $this->loadedMessages = Message::where("conversation_id",$this->selectedConversation->id)
        ->where(function($query) use ($user_id){
            $query->where('sender_id',$user_id)
            ->whereNull('sender_deleted_at');
        })->orWhere(function($query) use ($user_id){
            $query->where('receiver_id',$user_id)
            ->whereNull('receiver_deleted_at');
        })
        ->count();
        #skip and query
        $this->loadedMessages = Message::where("conversation_id",$this->selectedConversation->id)
        ->where(function($query) use ($user_id){
            $query->where('sender_id',$user_id)
            ->whereNull('sender_deleted_at');
        })->orWhere(function($query) use ($user_id){
            $query->where('receiver_id',$user_id)
            ->whereNull('receiver_deleted_at');
        })
        ->skip($count - $this->paginate_var)
        ->take($this->paginate_var)
        ->get();
    }
      #########################################
    public function sendMessage(){
        $this->validate(['body'=>'required|string']);

        $this->createdMessage= Message::create([
            'conversation_id'=>$this->selectedConversation->id,
            'sender_id'=>auth()->id(),
            'receiver_id'=>$this->selectedConversation->getReceiver()->id,
            'body'=>$this->body

        ]);

        $this->reset('body');

        #scroll to bottom

        $this->dispatch('scroll-bottom');


        #push the message
        $this->loadedMessages->push($this->createdMessage);

        #update conversation model
        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        #refresh chat list
        $this->dispatch('refresh')->to(ChatList::class);

        #broadcast
        $this->dispatch('dispatchMessageSent')->self();
       
    }
    public function dispatchMessageSent(){
        broadcast(new MessageSent(
            auth()->user(),
            $this->createdMessage,
            $this->selectedConversation,
            $this->selectedConversation->getReceiver()->id
        ));
    }
    public function dispatchMessageRead(){
        broadcast(new MessageRead(
            $this->selectedConversation->id,
            $this->selectedConversation->getReceiver()->id
        ));
        

    }
      #########################################
    public function mount(){
        $this->loadMessages();
    }
    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
