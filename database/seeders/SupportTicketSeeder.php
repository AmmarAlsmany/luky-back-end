<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use App\Models\ServiceProvider;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample users
        $clients = User::where('user_type', 'client')->limit(5)->get();
        $providers = ServiceProvider::limit(3)->get();
        $admins = User::where('user_type', 'admin')->get();

        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Skipping support ticket seeding.');
            return;
        }

        $categories = ['technical', 'billing', 'booking', 'complaint', 'general'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'];

        $ticketData = [
            [
                'user_type' => 'client',
                'category' => 'billing',
                'subject' => 'Payment not reflecting in booking',
                'description' => 'I completed a booking with Lulu Beauty Lounge yesterday and paid via MyFatoorah. The amount (SAR 215) was deducted, but the booking still shows Awaiting payment. Please verify and confirm.',
                'priority' => 'high',
                'status' => 'in_progress',
                'messages' => [
                    'I have attached the transaction receipt and bank SMS screenshot.',
                    'Our team is investigating the payment gateway sync. We will update you shortly.',
                ]
            ],
            [
                'user_type' => 'client',
                'category' => 'booking',
                'subject' => 'Provider did not show up',
                'description' => 'I booked a home cleaning service for today at 10 AM. The provider confirmed but never showed up. I waited for 2 hours. This is very unprofessional.',
                'priority' => 'urgent',
                'status' => 'open',
                'messages' => [
                    'This is unacceptable. I need a full refund immediately.',
                ]
            ],
            [
                'user_type' => 'client',
                'category' => 'technical',
                'subject' => 'Unable to upload profile picture',
                'description' => 'I am trying to upload my profile picture but getting an error "File size too large" even though the image is only 500KB.',
                'priority' => 'low',
                'status' => 'resolved',
                'messages' => [
                    'Can you please fix this issue?',
                    'This has been resolved. We increased the upload limit to 5MB. Please try again.',
                    'Thank you! It works now.',
                ]
            ],
            [
                'user_type' => 'provider',
                'category' => 'billing',
                'subject' => 'Incorrect commission deduction',
                'description' => 'My last 3 bookings show a commission of 20% but my contract states 15%. Please check and refund the excess amount.',
                'priority' => 'high',
                'status' => 'waiting_customer',
                'messages' => [
                    'I have attached my signed contract for reference.',
                    'We are reviewing your contract. Can you please confirm your provider ID and the booking references?',
                ]
            ],
            [
                'user_type' => 'client',
                'category' => 'complaint',
                'subject' => 'Rude behavior from provider',
                'description' => 'The service provider was very rude and unprofessional during the service. I am very disappointed with the experience.',
                'priority' => 'medium',
                'status' => 'closed',
                'messages' => [
                    'I want to file a formal complaint and get a refund.',
                    'We sincerely apologize for this experience. We have taken action against the provider and processed your refund.',
                    'Thank you for handling this quickly.',
                ]
            ],
            [
                'user_type' => 'client',
                'category' => 'general',
                'subject' => 'How to cancel a booking?',
                'description' => 'I need to cancel my upcoming booking but cannot find the cancel button in the app. Can you please help?',
                'priority' => 'low',
                'status' => 'resolved',
                'messages' => [
                    'Go to My Bookings > Select the booking > Scroll down to find the Cancel button. You can cancel up to 24 hours before the scheduled time.',
                    'Found it, thanks!',
                ]
            ],
            [
                'user_type' => 'client',
                'category' => 'technical',
                'subject' => 'App keeps crashing on Android',
                'description' => 'The app crashes every time I try to view my booking history. I am using Samsung Galaxy S21.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'messages' => [
                    'This happens every single time. Very frustrating!',
                    'We are aware of this issue on Android 13. Our development team is working on a fix. Expected release: next week.',
                ]
            ],
            [
                'user_type' => 'provider',
                'category' => 'general',
                'subject' => 'How to update my service prices?',
                'description' => 'I want to update the pricing for my car wash services. Where can I do this in the provider dashboard?',
                'priority' => 'low',
                'status' => 'resolved',
                'messages' => [
                    'Go to Services > My Services > Click Edit on the service > Update pricing > Save changes.',
                    'Perfect, thank you!',
                ]
            ],
        ];

        foreach ($ticketData as $data) {
            // Get a random user based on type
            if ($data['user_type'] === 'client' && !$clients->isEmpty()) {
                $user = $clients->random();
                $userId = $user->id;
            } elseif ($data['user_type'] === 'provider' && !$providers->isEmpty()) {
                $user = $providers->random();
                $userId = $user->user_id ?? null;
                
                if (!$userId) continue;
            } else {
                continue;
            }

            // Create ticket
            $ticket = SupportTicket::create([
                'user_id' => $userId,
                'user_type' => $data['user_type'],
                'category' => $data['category'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'assigned_to' => $admins->isNotEmpty() ? $admins->random()->id : null,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Add messages
            foreach ($data['messages'] as $index => $messageText) {
                $isUserMessage = $index % 2 === 0; // Alternate between user and admin
                
                SupportTicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => $isUserMessage ? $userId : ($admins->isNotEmpty() ? $admins->random()->id : $userId),
                    'sender_type' => $isUserMessage ? 'user' : 'admin',
                    'message' => $messageText,
                    'created_at' => $ticket->created_at->addMinutes(($index + 1) * 30),
                ]);
            }

            // Set resolved/closed timestamps
            if ($data['status'] === 'resolved') {
                $ticket->update(['resolved_at' => now()->subDays(rand(1, 10))]);
            } elseif ($data['status'] === 'closed') {
                $ticket->update([
                    'resolved_at' => now()->subDays(rand(5, 15)),
                    'closed_at' => now()->subDays(rand(1, 5)),
                ]);
            }
        }

        $this->command->info('Support tickets seeded successfully!');
    }
}
