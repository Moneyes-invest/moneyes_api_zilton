<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Binance;

// PHP version check
if (version_compare(phpversion(), '7.0', '<=')) {
    fwrite(STDERR, 'Hi, PHP '.phpversion()." support will be removed very soon as part of continued development.\n");
    fwrite(STDERR, "Please consider upgrading.\n");
}

/**
 * Wrapper/Decorator for the binance api, providing rate limiting.
 *
 * Eg. Usage:
 * require 'vendor/autoload.php';
 * $api = new Binance\\API();
 * $api = new Binance\\RateLimiter($api);
 */
class RateLimiter extends API
{
    private $api                       = null;
    private $weights                   = null;
    private $ordersfunctions           = null;
    private $exchangeRequestsRateLimit = 10;
    private $exchangeOrdersRateLimit   = 10;
    private $exchangeOrdersDailyLimit  = 10;
    private $requestsQueue             = [];
    private $ordersQueue               = [];
    private $ordersDayQueue            = [];

    public function __construct($api)
    {
        $this->api = $api;

        $this->weights = [
            'account'           => 5,
            'addToTransfered'   => 0,
            'aggTrades'         => 1,
            'balances'          => 1,
            'bookPrices'        => 1,
            'buy'               => 1,
            'buyTest'           => 1,
            'cancel'            => 1,
            'candlesticks'      => 1,
            'chart'             => 0,
            'cumulative'        => 0,
            'depositAddress'    => 1,
            'depositHistory'    => 1,
            'assetDetail'       => 1,
            'depth'             => 1,
            'depthCache'        => 1,
            'displayDepth'      => 1,
            'exchangeInfo'      => 1,
            'first'             => 0,
            'getProxyUriString' => 0,
            'getRequestCount'   => 0,
            'getTransfered'     => 0,
            'highstock'         => 1,
            'history'           => 5,
            'keepAlive'         => 0,
            'kline'             => 1,
            'last'              => 0,
            'marketBuy'         => 1,
            'marketBuyTest'     => 1,
            'marketSell'        => 1,
            'marketSellTest'    => 1,
            'miniTicker'        => 1,
            'openOrders'        => 2,
            'order'             => 1,
            'orders'            => 5,
            'orderStatus'       => 1,
            'prevDay'           => 2,
            'prices'            => 1,
            'report'            => 0,
            'sell'              => 1,
            'sellTest'          => 1,
            'setProxy'          => 0,
            'sortDepth'         => 1,
            'terminate'         => 0,
            'ticker'            => 1,
            'time'              => 1,
            'trades'            => 5,
            'userData'          => 1,
            'useServerTime'     => 1,
            'withdraw'          => 1,
            'withdrawFee'       => 1,
            'withdrawHistory'   => 1,
        ];

        $this->ordersfunctions = [
            'buy',
            'buyTest',
            'cancel',
            'history',
            'marketBuy',
            'marketBuyTest',
            'marketSell',
            'marketSellTest',
            'openOrders',
            'order',
            'orders',
            'orderStatus',
            'sell',
            'sellTest',
            'trades',
        ];

        $this->init();
    }

    /**
     * magic get for private and protected members.
     *
     * @return null
     */
    public function __get(string $member)
    {
        return $this->api->$member;
    }

    /**
     * magic set for private and protected members.
     *
     * @param $member string the name of the member property
     * @param $value the value of the member property
     */
    public function __set(string $member, $value)
    {
        $this->api->$member = $value;
    }

    /**
     * magic call to redirect call to the API, capturing and monitoring the weight limit.
     *
     * @param $name the function to call
     * @param $arguments the paramters to the function
     */
    public function __call(string $name, array $arguments)
    {
        $weight = $this->weights[$name] ?? false;

        if ($weight && $weight > 0) {
            $this->requestsPerMinute();
            if (true === in_array($name, $this->ordersfunctions)) {
                $this->ordersPerSecond();
                $this->ordersPerDay();
            }

            $c_time = time();

            for ($w = 0; $w < $weight; ++$w) {
                $this->requestsQueue[] = $c_time;
            }

            if (true === in_array($name, $this->ordersfunctions)) {
                for ($w = 0; $w < $weight; ++$w) {
                    $this->ordersQueue[]    = $c_time;
                    $this->ordersDayQueue[] = $c_time;
                }
            }
        }

        return call_user_func_array([&$this->api, $name], $arguments);
    }

    private function init()
    {
        $exchangeLimits = $this->api->exchangeInfo()['rateLimits'];

        if (false === is_array($exchangeLimits)) {
            echo "Problem getting exchange limits\n";

            return;
        }

        foreach ($exchangeLimits as $exchangeLimit) {
            switch ($exchangeLimit['rateLimitType']) {
                case 'REQUESTS':
                    $this->exchangeRequestsRateLimit = round($exchangeLimit['limit'] * 0.95);
                    break;
                case 'ORDERS':
                    if ('SECOND' === $exchangeLimit['interval']) {
                        $this->exchangeOrdersRateLimit = round($exchangeLimit['limit'] * 0.9);
                    }
                    if ('DAY' === $exchangeLimit['interval']) {
                        $this->exchangeOrdersDailyLimit = round($exchangeLimit['limit'] * 0.98);
                    }
                    break;
            }
        }
    }

    private function requestsPerMinute()
    {
        // requests per minute restrictions
        if (0 === count($this->requestsQueue)) {
            return;
        }

        while (count($this->requestsQueue) > $this->exchangeOrdersDailyLimit) {
            $oldest = $this->requestsQueue[0] ?? time();
            while ($oldest < time() - 60) {
                array_shift($this->requestsQueue);
                $oldest = $this->requestsQueue[0] ?? time();
            }
            echo 'Rate limiting in effect for requests '.PHP_EOL;
            sleep(1);
        }
    }

    private function ordersPerSecond()
    {
        // orders per second restrictions
        if (0 === count($this->ordersQueue)) {
            return;
        }

        while (count($this->ordersQueue) > $this->exchangeOrdersRateLimit) {
            $oldest = $this->ordersQueue[0] ?? time();
            while ($oldest < time() - 1) {
                array_shift($this->ordersQueue);
                $oldest = $this->ordersQueue[0] ?? time();
            }
            echo 'Rate limiting in effect for orders '.PHP_EOL;
            sleep(1);
        }
    }

    private function ordersPerDay()
    {
        // orders per day restrictions
        if (0 === count($this->ordersDayQueue)) {
            return;
        }

        $yesterday = time() - (24 * 60 * 60);
        while (count($this->ordersDayQueue) > round($this->exchangeOrdersDailyLimit * 0.8)) {
            $oldest = $this->ordersDayQueue[0] ?? time();
            while ($oldest < $yesterday) {
                array_shift($this->ordersDayQueue);
                $oldest = $this->ordersDayQueue[0] ?? time();
            }
            echo 'Rate limiting in effect for daily order limits '.PHP_EOL;

            $remainingRequests = round($this->exchangeOrdersDailyLimit * 0.2);
            $remainingSeconds  = $this->ordersDayQueue[0] - $yesterday;

            $sleepTime = ($remainingSeconds > $remainingRequests) ? round($remainingSeconds / $remainingRequests) : 1;
            sleep($sleepTime);
        }
    }
}
