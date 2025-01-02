<?php

namespace Tests\Feature\Api\V1;

use Tests\Feature\Api\ApiTestCase;
use App\Models\Table;

class TableTest extends ApiTestCase
{
    public function test_admin_can_create_table()
    {
        $admin = $this->createAdmin();
        $token = $this->loginAs($admin);

        $tableData = [
            'number' => 1,
            'capacity' => 4,
            'status' => 'available'
        ];

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->postJson('/api/v1/tables', $tableData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'number',
                    'capacity',
                    'status',
                    'created_by',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'number' => $tableData['number'],
                    'capacity' => $tableData['capacity'],
                    'status' => $tableData['status']
                ]
            ]);
    }

    public function test_customer_cannot_create_table()
    {
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $tableData = [
            'number' => 1,
            'capacity' => 4,
            'status' => 'available'
        ];

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->postJson('/api/v1/tables', $tableData);

        $response->assertStatus(403);
    }

    public function test_can_list_tables()
    {
        $admin = $this->createAdmin();
        $token = $this->loginAs($admin);

        Table::factory()->count(3)->create([
            'created_by' => $admin->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->getJson('/api/v1/tables');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'number',
                        'capacity',
                        'status',
                        'created_by',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_get_single_table()
    {
        $admin = $this->createAdmin();
        $token = $this->loginAs($admin);

        $table = Table::factory()->create([
            'created_by' => $admin->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->getJson("/api/v1/tables/{$table->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'number',
                    'capacity',
                    'status',
                    'created_by',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $table->id,
                    'number' => $table->number,
                    'capacity' => $table->capacity,
                    'status' => $table->status
                ]
            ]);
    }

    public function test_admin_can_update_table()
    {
        $admin = $this->createAdmin();
        $token = $this->loginAs($admin);

        $table = Table::factory()->create([
            'created_by' => $admin->id
        ]);

        $updateData = [
            'capacity' => 6,
            'status' => 'occupied'
        ];

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->putJson("/api/v1/tables/{$table->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'number',
                    'capacity',
                    'status',
                    'created_by',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'capacity' => $updateData['capacity'],
                    'status' => $updateData['status']
                ]
            ]);
    }

    public function test_customer_cannot_update_table()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $table = Table::factory()->create([
            'created_by' => $admin->id
        ]);

        $updateData = [
            'capacity' => 6,
            'status' => 'occupied'
        ];

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->putJson("/api/v1/tables/{$table->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_table()
    {
        $admin = $this->createAdmin();
        $token = $this->loginAs($admin);

        $table = Table::factory()->create([
            'created_by' => $admin->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->deleteJson("/api/v1/tables/{$table->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Table deleted successfully'
            ]);

        $this->assertDatabaseMissing('tables', ['id' => $table->id]);
    }

    public function test_customer_cannot_delete_table()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $token = $this->loginAs($customer);

        $table = Table::factory()->create([
            'created_by' => $admin->id
        ]);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->deleteJson("/api/v1/tables/{$table->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tables', ['id' => $table->id]);
    }
}
