<?php

class Coinspot
{
    private $publicUrl;
    private $privateUrl;
    private $key;
    private $secret;
    private $methods;

    public function __construct($key, $secret)
    {
        $this->publicUrl = 'https://www.coinspot.com.au/api/ro';
        $this->privateUrl = 'https://www.coinspot.com.au/api/ro';
        $this->key = $key;
        $this->secret = $secret;
        $this->methods = [
            'public' => [
                '/latest'
            ],
            'private' => [
                '/orders', '/orders/history', '/my/coin/deposit',
                '/my/coin/send', '/quote/buy', '/quote/sell',
                '/my/balances', '/my/orders', '/my/buy',
                '/my/sell', '/my/buy/cancel', '/my/sell/cancel'
            ]
        ];
    }

    private function signRequest($data)
    {
        return hash_hmac('sha512', json_encode($data), $this->secret);
    }

    private function request($method, $data = [])
    {
        $ch = curl_init();
        $header = ['Content-type: application/json'];

        if (in_array($method, $this->methods['private'])) {
            $endpoint = $this->privateUrl . $method;
            $data['nonce'] = time();

            $header[] = 'sign: ' . $this->signRequest($data);
            $header[] = 'key: ' . $this->key;
          
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            $endpoint = $this->publicUrl . $method;
        }

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $request = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($request, true);

        if ($response['status'] === 'ok') {
            return $response;
        } else {
            die(json_encode($response) . PHP_EOL);
        }
    }

    public function latest()
    {
        return $this->request('/latest');
    }

    public function orders($coinType)
    {
        return $this->request('/orders', [
            'cointype' => $coinType
        ]);
    }

    public function orderHistory($coinType)
    {
        return $this->request('/orders/history', [
            'cointype' => $coinType
        ]);
    }

    public function depositCoins($coinType)
    {
        return $this->request('/my/coin/deposit', [
            'cointype' => $coinType
        ]);
    }

    public function sendCoins($coinType, $address, $amount)
    {
        return $this->request('/my/coin/send', [
            'cointype' => $coinType,
            'address' => $address,
            'amount' => $amount
        ]);
    }

    public function buyQuote($coinType, $amount)
    {
        return $this->request('/quote/buy', [
            'cointype' => $coinType,
            'amount' => $amount
        ]);
    }

    public function sellQuote($coinType, $amount)
    {
        return $this->request('/quote/sell', [
            'cointype' => $coinType,
            'amount' => $amount
        ]);
    }

    public function myBalances()
    {
        return $this->request('/my/balances');
    }

    public function myOrders()
    {
        return $this->request('/my/orders');
    }

    public function buyOrder($coinType, $amount, $rate)
    {
        return $this->request('/my/buy', [
            'cointype' => $coinType,
            'amount' => $amount,
            'rate' => $rate
        ]);
    }

    public function sellOrder($coinType, $amount, $rate)
    {
        return $this->request('/my/sell', [
            'cointype' => $coinType,
            'amount' => $amount,
            'rate' => $rate
        ]);
    }

    public function cancelBuyOrder($id)
    {
        return $this->request('/my/buy/cancel', [
            'id' => $id
        ]);
    }

    public function cancelSellOrder($id)
    {
        return $this->request('/my/sell/cancel', [
            'id' => $id
        ]);
    }
}
