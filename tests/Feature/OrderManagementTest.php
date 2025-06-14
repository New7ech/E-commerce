<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Article;
use App\Models\Categorie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->admin()->create();
        $this->adminUser->assignRole($adminRole);

        // Create some orders for the regular user
        Order::factory()->count(3)->for($this->user)->create()->each(function ($order) {
            OrderItem::factory()->count(2)->for($order)->create([
                'article_id' => Article::factory()->create()->id
            ]);
        });

        // Create some general orders that might not belong to this specific user
        Order::factory()->count(2)->create()->each(function ($order) {
            OrderItem::factory()->count(1)->for($order)->create([
                'article_id' => Article::factory()->create()->id
            ]);
        });
    }

    // --- Customer Order History Tests ---

    /** @test */
    public function authenticated_user_can_view_their_order_history()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('profile.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.orders');
        $response->assertViewHas('orders', function ($viewOrders) {
            return $viewOrders->count() === 3 && $viewOrders->every(fn($order) => $order->user_id === $this->user->id);
        });

        // Check if some details of an order are present
        $userOrder = $this->user->orders()->first();
        if ($userOrder) {
            $response->assertSee('Order #'.$userOrder->id);
            $response->assertSee(number_format($userOrder->total_amount, 2));
        }
    }

    /** @test */
    public function guest_user_cannot_view_order_history_and_is_redirected()
    {
        $response = $this->get(route('profile.orders'));
        $response->assertRedirect(route('login'));
    }

    // --- Admin Order Management Tests ---

    /** @test */
    public function admin_user_can_access_the_admin_order_listing_page()
    {
        $this->actingAs($this->adminUser);
        // Ensure the 'admin' middleware is correctly configured for this test to pass.
        // If 'admin' middleware just checks 'is_admin' field, this should work.
        // If it checks a role/permission, User model + Spatie setup needs that.
        // For this test, we assume 'is_admin' is sufficient or 'admin' middleware is permissive in test env.

        $response = $this->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
        $response->assertViewHas('orders'); // Should have all 5 orders
        $allOrdersCount = Order::count();
        $response->assertViewHas('orders', function ($viewOrders) use ($allOrdersCount) {
            return $viewOrders->total() === $allOrdersCount; // Using paginate, so check total()
        });
    }

    /** @test */
    public function non_admin_user_cannot_access_admin_order_listing_page()
    {
        $this->actingAs($this->user); // Regular user

        // This test depends heavily on how the 'admin' middleware is implemented.
        // A typical behavior is a 403 Forbidden or redirect.
        // If 'admin' middleware is not set up, this might pass or give 404 if routes are not loaded.
        // Assuming a 403 or redirect if middleware is active.
        $response = $this->get(route('admin.orders.index'));
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404, 302])); // 302 if redirected (e.g. to login or home)
    }

    /** @test */
    public function admin_user_can_view_a_single_orders_details()
    {
        $this->actingAs($this->adminUser);
        $order = Order::first(); // Get any order

        $response = $this->get(route('admin.orders.show', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.show');
        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $viewOrder->id === $order->id;
        });
        $response->assertSee('Order #'.$order->id);
        $response->assertSee($order->user->name); // Customer name
        foreach($order->items as $item) {
            $response->assertSee($item->article->name);
        }
    }

    /** @test */
    public function admin_user_can_update_an_order_status()
    {
        $this->actingAs($this->adminUser);
        $order = Order::first();
        $newStatus = 'shipped';

        $response = $this->patch(route('admin.orders.updateStatus', $order->id), ['status' => $newStatus]);

        $response->assertRedirect(route('admin.orders.show', $order->id));
        $response->assertSessionHas('success', 'Order status updated successfully.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => $newStatus,
        ]);
        $this->assertEquals($newStatus, $order->fresh()->status);
    }

    /** @test */
    public function updating_order_status_with_invalid_status_fails_validation()
    {
        $this->actingAs($this->adminUser);
        $order = Order::first();

        $response = $this->patch(route('admin.orders.updateStatus', $order->id), ['status' => 'invalid_status_here']);
        $response->assertSessionHasErrors('status');
    }
}
