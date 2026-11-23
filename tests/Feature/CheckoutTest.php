<?php

namespace Tests\Feature;

use App\Models\CanteenStatus;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_20000_with_cod_is_accepted(): void
    {
        config(['kantin.cod_max_amount' => 20000]);

        $user = User::factory()->create();
        $menu = $this->makeMenu(['harga' => 10000, 'stok' => 5]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => ['qty' => 2, 'note' => null]], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'cod']);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionMissing('cart');
        $response->assertSessionMissing('order_type');

        $this->assertSame(20000, $order->subtotal);
        $this->assertSame(1000, $order->biaya_admin);
        $this->assertSame(21000, $order->total_harga);
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame('cod_pending', $order->payment_status);
        $this->assertSame('baru', $order->order_status);
        $this->assertSame(3, $menu->fresh()->stok);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'qty' => 2,
            'harga_satuan' => 10000,
        ]);
    }

    public function test_total_20001_with_cod_is_rejected(): void
    {
        config(['kantin.cod_max_amount' => 20000]);

        $user = User::factory()->create();
        $menu = $this->makeMenu(['harga' => 20001, 'stok' => 5]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => null]], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'cod']);

        $response->assertRedirect(route('checkout.show'));
        $response->assertSessionHas('error');
        $response->assertSessionHas('cart', [$menu->id => ['qty' => 1, 'note' => null]]);
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(5, $menu->fresh()->stok);
    }

    public function test_total_20001_with_online_is_accepted_as_pending(): void
    {
        config(['kantin.cod_max_amount' => 20000]);

        $user = User::factory()->create();
        $menu = $this->makeMenu(['harga' => 20001, 'stok' => 5]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => 1], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'online']);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(20001, $order->subtotal);
        $this->assertSame(1000, $order->biaya_admin);
        $this->assertSame(21001, $order->total_harga);
        $this->assertSame('online', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('baru', $order->order_status);
        $this->assertSame(4, $menu->fresh()->stok);
    }

    public function test_subtotal_19500_with_cod_is_accepted_and_total_is_20500(): void
    {
        config(['kantin.cod_max_amount' => 20000, 'kantin.admin_fee' => 1000]);

        $user = User::factory()->create();
        $menu = $this->makeMenu(['harga' => 19500, 'stok' => 5]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => null]], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'cod']);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(19500, $order->subtotal);
        $this->assertSame(1000, $order->biaya_admin);
        $this->assertSame(20500, $order->total_harga);
    }

    public function test_admin_fee_is_saved_from_config(): void
    {
        config(['kantin.admin_fee' => 2500]);

        $user = User::factory()->create();
        $menu = $this->makeMenu(['harga' => 10000]);

        $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => null]], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'online']);

        $order = Order::firstOrFail();

        $this->assertSame(10000, $order->subtotal);
        $this->assertSame(2500, $order->biaya_admin);
        $this->assertSame(12500, $order->total_harga);
    }

    public function test_note_is_saved_to_order_item(): void
    {
        $user = User::factory()->create();
        $menu = $this->makeMenu();

        $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => null]], 'order_type' => 'takeaway'])
            ->patch(route('cart.note', $menu), ['note' => 'tidak pedas']);

        $this->post(route('checkout.store'), ['payment_method' => 'cod']);

        $order = Order::firstOrFail();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'catatan' => 'tidak pedas',
        ]);
    }

    public function test_note_longer_than_100_characters_is_rejected(): void
    {
        $user = User::factory()->create();
        $menu = $this->makeMenu();

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => 'aman']], 'order_type' => 'takeaway'])
            ->patch(route('cart.note', $menu), ['note' => str_repeat('a', 101)]);

        $response->assertSessionHasErrors('note');
        $this->assertSame('aman', session('cart')[$menu->id]['note']);
    }

    public function test_script_in_note_is_not_rendered_as_raw_html(): void
    {
        $user = User::factory()->create();
        $menu = $this->makeMenu();
        $rawNote = "pedas <script>alert('xss')</script> es dipisah";

        $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => null]], 'order_type' => 'takeaway'])
            ->patch(route('cart.note', $menu), ['note' => $rawNote]);

        $this->post(route('checkout.store'), ['payment_method' => 'cod']);

        $order = Order::firstOrFail();

        $this->assertStringNotContainsString('<script>', $order->items()->firstOrFail()->catatan);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertDontSee($rawNote, false)
            ->assertSee('Catatan:', false);
    }

    public function test_guest_can_save_note_but_checkout_redirects_to_login(): void
    {
        $menu = $this->makeMenu();

        $response = $this->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => null]]])
            ->patch(route('cart.note', $menu), ['note' => 'es dipisah']);

        $response->assertRedirect();
        $this->assertSame('es dipisah', session('cart')[$menu->id]['note']);

        $this->get(route('checkout.show'))->assertRedirect(route('login'));
    }

    public function test_legacy_cart_format_still_checks_out(): void
    {
        $user = User::factory()->create();
        $menu = $this->makeMenu(['harga' => 10000]);

        $this->withSession(['cart' => [$menu->id => 1], 'order_type' => 'takeaway'])
            ->get(route('cart.index'))
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => 1], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'online']);

        $order = Order::firstOrFail();

        $this->assertSame(10000, $order->subtotal);
        $this->assertSame(1000, $order->biaya_admin);
        $this->assertSame(11000, $order->total_harga);
    }

    public function test_checkout_page_renders_with_cod_disabled_above_limit(): void
    {
        config(['kantin.cod_max_amount' => 20000]);

        $user = User::factory()->create();
        $menu = $this->makeMenu(['harga' => 20001, 'stok' => 5]);

        $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => 1], 'order_type' => 'takeaway'])
            ->get(route('checkout.show'))
            ->assertOk()
            ->assertSee('Buat Pesanan')
            ->assertSee('Rp21.001')
            ->assertSee('Tidak tersedia karena subtotal menu melebihi batas COD');
    }

    public function test_cart_page_shows_login_checkout_for_guest(): void
    {
        $menu = $this->makeMenu(['stok' => 5]);

        $this->withSession(['cart' => [$menu->id => 1], 'order_type' => 'takeaway'])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Login untuk Checkout')
            ->assertSee('bisa dibayar COD atau online')
            ->assertSee('Biaya admin')
            ->assertSee('dihitung dari subtotal menu');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('checkout.show'))->assertRedirect(route('login'));
    }

    public function test_empty_cart_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'online']);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_dine_in_is_rejected_when_canteen_is_full(): void
    {
        CanteenStatus::current()->update(['status' => 'penuh']);

        $user = User::factory()->create();
        $menu = $this->makeMenu();

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => 1], 'order_type' => 'dine_in'])
            ->post(route('checkout.store'), ['payment_method' => 'online']);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
        $response->assertSessionMissing('order_type');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $menu->fresh()->stok);
    }

    public function test_insufficient_stock_is_rejected_and_stock_is_unchanged(): void
    {
        $user = User::factory()->create();
        $menu = $this->makeMenu(['stok' => 1]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => 2], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'online']);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(1, $menu->fresh()->stok);
    }

    public function test_unavailable_menu_is_rejected(): void
    {
        $user = User::factory()->create();
        $menu = $this->makeMenu(['is_available' => false]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$menu->id => 1], 'order_type' => 'takeaway'])
            ->post(route('checkout.store'), ['payment_method' => 'online']);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $order = Order::create([
            'user_id' => $owner->id,
            'order_type' => 'takeaway',
            'payment_method' => 'cod',
            'subtotal' => 10000,
            'biaya_admin' => 0,
            'total_harga' => 10000,
            'payment_status' => 'cod_pending',
            'order_status' => 'baru',
        ]);

        $this->actingAs($other)
            ->get(route('orders.show', $order))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Detail Pesanan');
    }

    public function test_login_returns_to_checkout_and_keeps_cart(): void
    {
        $user = User::factory()->create();
        $menu = $this->makeMenu(['stok' => 5]);

        $this->withSession(['cart' => [$menu->id => ['qty' => 1, 'note' => 'tanpa es']], 'order_type' => 'takeaway'])
            ->get(route('checkout.show'))
            ->assertRedirect(route('login'));

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('checkout.show'));
        $response->assertSessionHas('cart', [$menu->id => ['qty' => 1, 'note' => 'tanpa es']]);
        $response->assertSessionHas('order_type', 'takeaway');
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeMenu(array $overrides = []): Menu
    {
        return Menu::create(array_merge([
            'nama' => 'Menu Uji',
            'kategori' => 'makanan',
            'harga' => 10000,
            'stok' => 10,
            'is_available' => true,
        ], $overrides));
    }
}
