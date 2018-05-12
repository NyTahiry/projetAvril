<?php

namespace App\Repositories;

use App\Models\Thread;
use App\Models\Message;
use App\Models\User;

class MessageRepository
{
    public function deleteMessages($conversationId)
    {
        $delete = Message::where('thread_id', $conversationId)->delete();
        if ($delete) {
            return true;
        }

        return false;
    }

    public function softDeleteMessage($messageId, $authUserId)
    {
        $message = $this->with(['thread_id' => function ($q) use ($authUserId) {
            $q->where('user_one', $authUserId);
            $q->orWhere('user_two', $authUserId);
        }])->find($messageId);

        if (is_null($message->conversation)) {
            return false;
        }

        if ($message->user_id == $authUserId) {
            $message->deleted_from_sender = 1;
        } else {
            $message->deleted_from_receiver = 1;
        }

        return (boolean) $this->update($message);
        
    }
}