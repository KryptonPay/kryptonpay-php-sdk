<?php

namespace KryptonPay\Api;

class ApiContext extends DefaultModel
{
    public const SLIPBANK = 1;
    public const CREDIT_CARD = 2;

    public const PERSON_NATURAL = 1;
    public const PERSON_LEGAL = 2;

    private $token;
    private $isSandBox = false;
    private $transaction;

    public function __construct()
    {
    }

    public function setApiToken(string $token)
    {
        $this->token = $token;
    }

    public function setIsSandbox(bool $isSandBox)
    {
        $this->isSandBox = $isSandBox;
    }

    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function getApiToken()
    {
        return $this->token;
    }

    public function getIsSandbox()
    {
        return $this->isSandBox;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }
}
