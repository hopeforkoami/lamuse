<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Song;
use App\Models\Entitlement;
use App\Services\NotificationService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OrderController
{
    protected $notifications;

    public function __construct()
    {
        $this->notifications = new NotificationService();
    }

    public function createOrder(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $songIds = $data['song_ids'] ?? [];
        $email = $data['email'] ?? '';
        $paymentProvider = $data['payment_provider'] ?? 'paypal';

        // Check if user is logged in
        $token = $request->getAttribute("token");
        $userId = $token ? $token['uid'] : null;

        if (!$userId && empty($email)) {
            $response->getBody()->write(json_encode(['error' => 'Email is required for guest checkout']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (empty($songIds)) {
            $response->getBody()->write(json_encode(['error' => 'No songs selected']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $songs = Song::findMany($songIds);
        $totalAmount = $songs->sum('price');
        $currencyCode = $songs->first()->currency_code ?? 'XOF';

        $order = Order::create([
            'user_id' => $userId,
            'email' => $userId ? null : $email,
            'total_amount' => $totalAmount,
            'currency_code' => $currencyCode,
            'status' => 'pending',
            'payment_provider' => $paymentProvider
        ]);

        foreach ($songs as $song) {
            OrderItem::create([
                'order_id' => $order->id,
                'song_id' => $song->id,
                'price' => $song->price
            ]);
        }

        // Return order details and payment initiation data
        $result = [
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'currency' => $order->currency_code,
            'payment_url' => $this->getPaymentUrl($order)
        ];

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getPaymentUrl(Order $order)
    {
        // Mock payment URL generation based on provider
        return "/api/payments/initiate/" . $order->payment_provider . "/" . $order->id;
    }

    public function handleWebhook(Request $request, Response $response, array $args): Response
    {
        $provider = $args['provider'];
        $data = $request->getParsedBody();

        // Validate webhook signature here based on provider...

        $orderId = $data['order_id'] ?? null;
        $order = Order::find($orderId);

        if ($order && $order->status === 'pending') {
            $order->status = 'paid';
            $order->payment_id = $data['transaction_id'] ?? 'N/A';
            $order->save();

            $entitlements = $this->createEntitlements($order);
            $this->notifications->sendOrderReceipt($order->email ?? $order->user->email, $order, $entitlements);
        }

        return $response->withStatus(200);
    }

    private function createEntitlements(Order $order)
    {
        foreach ($order->items as $item) {
            Entitlement::create([
                'user_id' => $order->user_id,
                'email' => $order->email,
                'song_id' => $item->song_id,
                'order_id' => $order->id,
                'access_token' => bin2hex(random_bytes(16)),
                'expires_at' => null // Permanent access by default
            ]);
        }
    }
}
