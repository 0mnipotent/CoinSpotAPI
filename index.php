<?php


require_once 'coinspot.php';

$key = 'REMOVED';
$secret = 'REMOVED';


$coinspot = new Coinspot($key, $secret);
// $coins = ['btc', 'eth', 'xrp', 'ltc', 'dash', 'xmr', 'etc', 'xem', 'rep', 'maid', 'zec', 'doge', 'fct', 'ppc', 'nxt'];
$coins = ['btc', 'eth', 'xrp', 'ltc', 'dash', 'xmr', 'etc', 'xem', 'rep', 'maid', 'zec', 'doge', 'fct', 'ppc', 'nxt'];
$results = [];

echo '<h2>Function To Get CoinSpot My Balances </h2><br /><br />';

foreach ($coins as $coin) {
  $results[$coin] = $coinspot->myBalances($coin, 1);
  sleep(1);
}

echo '<pre>';print_r($results);'</pre>';
