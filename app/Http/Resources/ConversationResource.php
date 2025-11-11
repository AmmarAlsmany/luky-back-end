<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $userType = $user->hasRole('client') ? 'client' : 'provider';

        return [
            'id' => $this->id,
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ],
            'provider' => [
                'id' => $this->provider->id,
                'business_name' => $this->provider->business_name,
                'logo_url' => $this->provider->logo_url,
            ],
            'booking_id' => $this->booking_id,
            'last_message' => $this->whenLoaded('lastMessage', function () {
                return $this->lastMessage ? new MessageResource($this->lastMessage) : null;
            }),
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'unread_count' => $userType === 'client' ? $this->client_unread_count : $this->provider_unread_count,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
