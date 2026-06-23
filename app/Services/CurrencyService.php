<?php

namespace App\Services;

use App\Models\Currency;
use App\Services\SettingService;

class CurrencyService
{
    public function getDefaultCurrency()
    {

        static $currency = null;

        if ($currency != null) {
            return $currency;
        }

        // Fetch default currency using Eloquent
        $currency = Currency::where('is_default', 1)->first();



        return $currency;
    }
    public function getAllCurrency()
    {


        static $currencies = null;

        if ($currencies != null) {
            return $currencies;
        }


        $currencies = Currency::all()->toArray();



        return $currencies;
    }
    public function getCurrencyCodeSettings($code, $fetchWithSymbol = false)
    {
        static $cache = [];

        $key = ($fetchWithSymbol ? 'symbol:' : 'code:') . $code;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $query = Currency::select('*');

        if ($fetchWithSymbol) {
            $query->where('symbol', $code);
        } else {
            $query->where('code', $code);
        }

        $currency = $query->get()->toArray();

        $cache[$key] = $currency;

        return $currency;
    }
    public function currentCurrencyPrice($price, $with_symbol = false)
    {
        $system_settings = app(SettingService::class)->getSettings('system_settings', true);
        $system_settings = json_decode($system_settings, true);

        // Check if Shiprocket shipping is enabled
        $shipping_settings = app(SettingService::class)->getSettings('shipping_method', true);
        $shipping_settings = json_decode($shipping_settings, true);
      
        // If Shiprocket is enabled, return price as-is in INR without conversion
        if (isset($shipping_settings['shiprocket_shipping_method']) && $shipping_settings['shiprocket_shipping_method'] == 1) {
          // Get INR currency details
            $inr_details = $this->getCurrencyCodeSettings('INR');
            $inr_symbol = $inr_details[0]['symbol'] ?? '₹';
            
            return $with_symbol ? $inr_symbol . number_format((float) $price, 2) : (float) $price;
        }

        // 1. Get currency code: session first, fallback to default
        $currency_code = session('currency') ?? $system_settings['currency_setting']['code'];

        // 2. Get currency details
        $currency_details = $this->getCurrencyCodeSettings($currency_code);
        $exchange_rate = (float) $currency_details[0]['exchange_rate'];
        $currency_symbol = $currency_details[0]['symbol'] ?? $system_settings['currency_setting']['symbol'];

        // 3. Calculate converted amount
        $amount = (float) $price * $exchange_rate;

        return $with_symbol ? $currency_symbol . number_format($amount, 2) : $amount;
    }
    public function getPriceCurrency($price)
    {
        // Check if Shiprocket shipping is enabled
        $shipping_settings = app(SettingService::class)->getSettings('shipping_method', true);
        $shipping_settings = json_decode($shipping_settings, true);
        
        // If Shiprocket is enabled, return only INR currency data without conversion
        if (isset($shipping_settings['shiprocket_shipping_method']) && $shipping_settings['shiprocket_shipping_method'] == 1) {
            // Get INR currency details
            $inr_details = $this->getCurrencyCodeSettings('INR');
            
            if (!empty($inr_details)) {
                return [
                    'INR' => [
                        'currency_code' => 'INR',
                        'symbol' => $inr_details[0]['symbol'] ?? '₹',
                        'exchange_rate' => '1.00',
                        'amount' => formatePriceDecimal((float) $price)
                    ]
                ];
            }
        }

        $currencies = app(CurrencyService::class)->getAllCurrency();
        // dd($currencies);
        $rows = [];

        foreach ($currencies as $currency) {
            // Make sure $currency is an object
            // if (!is_object($currency)) {
            //     continue; // skip if it's not an object
            // }

            // Calculate the amount in target currency
            $exchangeRate = (float) $currency['exchange_rate'];
            $amount = (float) $price * $exchangeRate;

            // Format and build the result row
            $rows[$currency['code']] = [
                'currency_code' => $currency['code'],
                'symbol' => $currency['symbol'],
                'exchange_rate' => number_format($exchangeRate, 2),
                'amount' => formatePriceDecimal($amount)
            ];
        }

        return $rows;
    }

    public function formateCurrency($price, $currency = '', $before = true)
    {
        $baseCurrency = app(CurrencyService::class)->getDefaultCurrency()->symbol;

        $currency_symbol = isset($currency) && !empty($currency) ? $currency : $baseCurrency;
        if ($before == true) {
            return $currency_symbol . $price;
        } else {
            return $price . $currency_symbol;
        }
    }
}
