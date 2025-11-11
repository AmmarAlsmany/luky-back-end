<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\ServiceProvider;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample users
        $clients = User::where('user_type', 'client')->limit(5)->get();
        $providers = ServiceProvider::limit(5)->get();

        if ($clients->isEmpty() || $providers->isEmpty()) {
            $this->command->warn('Need both clients and providers. Skipping conversation seeding.');
            return;
        }

        $conversationData = [
            [
                'messages' => [
                    ['sender' => 'client', 'text' => 'Hello, I need help with my booking'],
                    ['sender' => 'provider', 'text' => 'Your booking is scheduled for tomorrow at 3 PM. Is there anything you need?'],
                    ['sender' => 'client', 'text' => 'Can I change the time to 5 PM?'],
                    ['sender' => 'provider', 'text' => 'Let me check... Yes, I can change it to 5 PM for you.'],
                    ['sender' => 'client', 'text' => 'Perfect, thank you!'],
                ]
            ],
            [
                'messages' => [
                    ['sender' => 'provider', 'text' => 'I have a question about the booking'],
                    ['sender' => 'client', 'text' => 'Sure, what would you like to know?'],
                    ['sender' => 'provider', 'text' => 'What time should I arrive?'],
                    ['sender' => 'client', 'text' => 'Please arrive at 3:00 PM. The address is in the booking details.'],
                ]
            ],
            [
                'messages' => [
                    ['sender' => 'client', 'text' => 'Can you confirm the service price?'],
                    ['sender' => 'provider', 'text' => 'Yes, the total is SAR 250 for the full service.'],
                    ['sender' => 'client', 'text' => 'Great, see you tomorrow!'],
                ]
            ],
            [
                'messages' => [
                    ['sender' => 'client', 'text' => 'Do you offer home service?'],
                    ['sender' => 'provider', 'text' => 'Yes! We provide home service with an additional SAR 50 fee.'],
                    ['sender' => 'client', 'text' => 'Perfect, I would like to book for home service'],
                    ['sender' => 'provider', 'text' => 'Great! Please proceed with the booking and select "Home Service" option.'],
                ]
            ],
            [
                'messages' => [
                    ['sender' => 'provider', 'text' => 'Thank you for booking with us!'],
                    ['sender' => 'client', 'text' => 'You\'re welcome! Looking forward to it.'],
                ]
            ],
        ];

        foreach ($conversationData as $index => $data) {
            if ($index >= $clients->count() || $index >= $providers->count()) {
                break;
            }

            $client = $clients[$index];
            $provider = $providers[$index];

            // Create conversation
            $conversation = Conversation::create([
                'client_id' => $client->id,
                'provider_id' => $provider->id,
                'booking_id' => null,
                'last_message_at' => now()->subHours(rand(1, 48)),
            ]);

            // Create messages
            $lastMessage = null;
            foreach ($data['messages'] as $msgIndex => $msg) {
                $isClient = $msg['sender'] === 'client';
                
                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $isClient ? $client->id : $provider->user_id,
                    'sender_type' => $isClient ? 'client' : 'provider',
                    'message_type' => 'text',
                    'content' => $msg['text'],
                    'is_read' => $msgIndex < count($data['messages']) - 1, // Last message unread
                    'read_at' => $msgIndex < count($data['messages']) - 1 ? now() : null,
                ]);

                $lastMessage = $message;
            }

            // Update conversation with last message
            if ($lastMessage) {
                $conversation->update([
                    'last_message_id' => $lastMessage->id,
                    'last_message_at' => $lastMessage->created_at,
                ]);
            }
        }

        $this->command->info('Conversations seeded successfully!');
    }
}
