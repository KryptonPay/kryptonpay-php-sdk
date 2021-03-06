<?php

namespace KryptonPay\Service\Transaction;

use KryptonPay\Api\ApiContext;
use KryptonPay\Api\KryptonPay;
use KryptonPay\Api\Transaction;

class GetTransaction extends Transaction
{
    protected $apiContext;
    protected $kryptonPay;

    public function __construct(ApiContext $apiContext)
    {
        $this->apiContext = $apiContext;
        $this->kryptonPay = new KryptonPay();
    }

    public function execute()
    {
        $transaction = $this->kryptonPay->getTransaction($this->apiContext, $this->getReference());
        unset($transaction->meta);
        return $transaction;
    }
}
