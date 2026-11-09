<?php

namespace Tests\Feature;

use App\Models\CanteenStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanteenStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_canteen_status(): void
    {
        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();

        $response = $this->actingAs($admin)->patch(route('admin.canteen-status.update'), [
            'status' => 'penuh',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('canteen_statuses', [
            'status' => 'penuh',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_admin_pages_render_with_admin_layout(): void
    {
        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Admin Panel');

        $this->actingAs($admin)->get(route('admin.menus.index'))
            ->assertOk()
            ->assertSee('Kelola Menu');

        $this->actingAs($admin)->get(route('admin.menus.create'))
            ->assertOk()
            ->assertSee('Tambah Menu');
    }

    public function test_customer_can_save_takeaway_order_type_in_session(): void
    {
        $response = $this->patch(route('cart.order-type'), [
            'order_type' => 'takeaway',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame('takeaway', session('order_type'));
    }

    public function test_dine_in_is_rejected_when_canteen_is_full(): void
    {
        CanteenStatus::current()->update(['status' => 'penuh']);

        $response = $this->from(route('cart.index'))->patch(route('cart.order-type'), [
            'order_type' => 'dine_in',
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
        $this->assertNull(session('order_type'));
    }

    public function test_dine_in_selection_is_cleared_when_canteen_becomes_full(): void
    {
        $this->withSession(['order_type' => 'dine_in']);
        CanteenStatus::current()->update(['status' => 'penuh']);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSessionMissing('order_type');
    }
}
