<?php

namespace Tests\Feature\Api\V1;

use Tests\Feature\Api\ApiTestCase;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationTest extends ApiTestCase
{
    public function test_customer_can_create_reservation()
    {
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $table = Table::factory()->available()->create();

        $reservationData = [
            'table_id' => $table->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time' => '19:00:00',
            'guests' => 4,
            'notes' => 'Birthday celebration'
        ];

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->postJson('/api/v1/reservations', $reservationData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'table_id',
                    'user_id',
                    'date',
                    'time',
                    'guests',
                    'notes',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'table_id' => $table->id,
                    'user_id' => $customer->id,
                    'date' => $reservationData['date'],
                    'time' => $reservationData['time'],
                    'guests' => $reservationData['guests'],
                    'notes' => $reservationData['notes']
                ]
            ]);
    }

    public function test_cannot_create_reservation_for_occupied_table()
    {
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $table = Table::factory()->occupied()->create();

        $reservationData = [
            'table_id' => $table->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time' => '19:00:00',
            'guests' => 4
        ];

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->postJson('/api/v1/reservations', $reservationData);

        $response->assertStatus(422);
    }

    public function test_customer_can_view_own_reservations()
    {
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $reservations = Reservation::factory()->count(3)->create([
            'user_id' => $customer->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->getJson('/api/v1/reservations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'table_id',
                        'user_id',
                        'date',
                        'time',
                        'guests',
                        'notes',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_admin_can_view_all_reservations()
    {
        $admin = $this->createAdmin();
        $token = $this->loginAs($admin);

        Reservation::factory()->count(3)->create();

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->getJson('/api/v1/reservations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'table_id',
                        'user_id',
                        'date',
                        'time',
                        'guests',
                        'notes',
                        'status'
                    ]
                ]
            ]);
    }

    public function test_customer_can_view_own_reservation()
    {
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $reservation = Reservation::factory()->create([
            'user_id' => $customer->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->getJson("/api/v1/reservations/{$reservation->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'table_id',
                    'user_id',
                    'date',
                    'time',
                    'guests',
                    'notes',
                    'status'
                ]
            ]);
    }

    public function test_customer_cannot_view_others_reservation()
    {
        $customer = $this->createCustomer();
        $otherCustomer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $reservation = Reservation::factory()->create([
            'user_id' => $otherCustomer->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->getJson("/api/v1/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }

    public function test_customer_can_update_own_reservation()
    {
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $reservation = Reservation::factory()->create([
            'user_id' => $customer->id
        ]);

        $updateData = [
            'guests' => 6,
            'notes' => 'Updated celebration details'
        ];

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->putJson("/api/v1/reservations/{$reservation->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'guests' => $updateData['guests'],
                    'notes' => $updateData['notes']
                ]
            ]);
    }

    public function test_customer_can_cancel_own_reservation()
    {
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $reservation = Reservation::factory()->create([
            'user_id' => $customer->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->deleteJson("/api/v1/reservations/{$reservation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Reservation cancelled successfully'
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_customer_cannot_cancel_others_reservation()
    {
        $customer = $this->createCustomer();
        $otherCustomer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $reservation = Reservation::factory()->create([
            'user_id' => $otherCustomer->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->deleteJson("/api/v1/reservations/{$reservation->id}");

        $response->assertStatus(403);
    }
}
