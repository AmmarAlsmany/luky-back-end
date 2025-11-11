<?php

use App\Models\Conversation;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Private channel for conversation messages
 * Users can only listen to conversations they are part of
 */
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    // Check if user is the client in this conversation
    if ($conversation->client_id === $user->id) {
        return ['id' => $user->id, 'name' => $user->name, 'type' => 'client'];
    }

    // Check if user is the provider in this conversation
    $provider = ServiceProvider::where('user_id', $user->id)->first();
    if ($provider && $conversation->provider_id === $provider->id) {
        return ['id' => $user->id, 'name' => $user->name, 'type' => 'provider'];
    }

    return false;
});

/**
 * Private channel for typing indicators
 */
Broadcast::channel('conversation.{conversationId}.typing', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    // Check if user is part of this conversation
    if ($conversation->client_id === $user->id) {
        return true;
    }

    $provider = ServiceProvider::where('user_id', $user->id)->first();
    if ($provider && $conversation->provider_id === $provider->id) {
        return true;
    }

    return false;
});
