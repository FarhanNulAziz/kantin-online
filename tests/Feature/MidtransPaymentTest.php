<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_get_token_for_online_pending_order(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOnlinePendingOrder($user);

        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldReceive('createSnapToken')->once()->andReturnUsing(function (Order $order) {
                $order->setAttribute('_midtrans_order_id', 'KANTIN-'.$order->id.'-123456');
                return 'snap-token-123';
            });
        });

        $response = $this->actingAs($user)->postJson(route('orders.pay', $order));

        $response->assertOk()->assertJson(['token' => 'snap-token-123']);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'snap_token' => 'snap-token-123',
            'transaction_status' => 'pending',
            'jumlah' => $order->total_harga,
        ]);
    }

    public function test_other_user_is_forbidden(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->makeOnlinePendingOrder($owner);

        $this->actingAs($other)
            ->postJson(route('orders.pay', $order))
            ->assertForbidden();
    }

    public function test_cod_order_is_rejected(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_type' => 'takeaway',
            'payment_method' => 'cod',
            'total_harga' => 10000,
            'payment_status' => 'cod_pending',
            'order_status' => 'baru',
        ]);

        $this->actingAs($user)
            ->postJson(route('orders.pay', $order))
            ->assertStatus(422);
    }

    public function test_paid_order_is_rejected(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOnlinePendingOrder($user, ['payment_status' => 'paid']);

        $this->actingAs($user)
            ->postJson(route('orders.pay', $order))
            ->assertStatus(422);
    }

    public function test_cancelled_order_is_rejected(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOnlinePendingOrder($user, ['order_status' => 'dibatalkan']);

        $this->actingAs($user)
            ->postJson(route('orders.pay', $order))
            ->assertStatus(422);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOnlinePendingOrder($user);

        $this->post(route('orders.pay', $order))->assertRedirect(route('login'));
    }

    public function test_item_details_sum_equals_gross_amount_including_admin_fee(): void
    {
        $user = User::factory()->create(['name' => 'Budi', 'email' => 'budi@example.com']);
        $menu = Menu::create([
            'nama' => 'Nasi Goreng Spesial Panjang Sekali Sampai Lebih Lima Puluh Karakter Tambahan',
            'kategori' => 'makanan',
            'harga' => 15000,
            'stok' => 10,
            'is_available' => true,
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'order_type' => 'takeaway',
            'payment_method' => 'online',
            'total_harga' => 32000,
            'payment_status' => 'pending',
            'order_status' => 'baru',
        ]);
        $order->items()->create(['menu_id' => $menu->id, 'qty' => 2, 'harga_satuan' => 15000]);
        $order->setAttribute('biaya_admin', 2000);

        $service = new MidtransService();
        $payload = $service->buildPayload($order);

        $sum = 0;
        foreach ($payload['item_details'] as $detail) {
            $sum += $detail['price'] * $detail['quantity'];
        }

        $this->assertSame(32000, $payload['transaction_details']['gross_amount']);
        $this->assertSame($payload['transaction_details']['gross_amount'], $sum);
        $this->assertSame('ADMIN-FEE', $payload['item_details'][1]['id']);
        $this->assertSame(2000, $payload['item_details'][1]['price']);
        $this->assertLessThanOrEqual(50, mb_strlen($payload['item_details'][0]['name']));
        $this->assertLessThanOrEqual(50, strlen($payload['transaction_details']['order_id']));
    }

    public function test_existing_pending_payment_token_is_reused(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOnlinePendingOrder($user);

        Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'KANTIN-'.$order->id.'-old',
            'transaction_status' => 'pending',
            'snap_token' => 'reuse-token',
            'jumlah' => $order->total_harga,
        ]);

        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldNotReceive('createSnapToken');
        });

        $response = $this->actingAs($user)->postJson(route('orders.pay', $order));

        $response->assertOk()->assertJson(['token' => 'reuse-token']);
        $this->assertSame(1, Payment::where('order_id', $order->id)->count());
    }

    public function test_server_key_not_present_in_order_show_html(): void
    {
        config(['services.midtrans.server_key' => 'SB-Mid-server-SECRET', 'services.midtrans.client_key' => 'SB-Mid-client-PUBLIC']);
        $user = User::factory()->create();
        $order = $this->makeOnlinePendingOrder($user);

        $response = $this->actingAs($user)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertDontSee('SB-Mid-server-SECRET', false);
        $response->assertSee('Bayar Sekarang', false);
        $response->assertSee('SB-Mid-client-PUBLIC', false);
    }

    private function makeOnlinePendingOrder(User $user, array $overrides = []): Order
    {
        $menu = Menu::create([
            'nama' => 'Menu Uji',
            'kategori' => 'makanan',
            'harga' => 10000,
            'stok' => 10,
            'is_available' => true,
        ]);

        $order = Order::create(array_merge([
            'user_id' => $user->id,
            'order_type' => 'takeaway',
            'payment_method' => 'online',
            'total_harga' => 10000,
            'payment_status' => 'pending',
            'order_status' => 'baru',
        ], $overrides));

        $order->items()->create([
            'menu_id' => $menu->id,
            'qty' => 1,
            'harga_satuan' => 10000,
        ]);

        return $order;
    }
}
