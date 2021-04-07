<?php

namespace KryptonPay\Service\Register;

use KryptonPay\Api\User;
use KryptonPay\Api\ApiContext;
use KryptonPay\Api\KryptonPay;

class ContractRegister
{
    protected $apiContext;
    protected $kryptonPay;

    public function __construct(ApiContext $apiContext)
    {
        $this->apiContext = $apiContext;
        $this->kryptonPay = new KryptonPay();
    }

    public function createContract($dataContrato)
    {
        return $this->kryptonPay->createContract($this->apiContext, $dataContrato);
    }

    public function updateContract($dataContrato)
    {
        return $this->kryptonPay->updateContract($this->apiContext, $dataContrato);
    }

    public function getContract($idContrato)
    {
        return $this->kryptonPay->getContract($this->apiContext, $idContrato);
    }

    public function getContractByCpfCnpj($cpfCnpj)
    {
        return $this->kryptonPay->getContractByCpfCnpj($this->apiContext, $cpfCnpj);
    }

    public function allContracts()
    {
        return $this->kryptonPay->allContracts($this->apiContext);
    }

    public function allSubContractById($idContractFather, $idContract, $params = null)
    {
        $paramsString = $this->paramsString($params);

        return $this->kryptonPay->allSubContractById($this->apiContext, $idContractFather, $idContract, $paramsString);
    }

    public function allSubContracts($idContractFather, $params = null)
    {
        $paramsString = $this->paramsString($params);

        return $this->kryptonPay->allSubContracts($this->apiContext, $idContractFather, $paramsString);
    }

    public function paramsString($arrayParams)
    {
        $paramsString = '';

        if($arrayParams != null)
        {
            foreach($arrayParams as $key => $param) {
                $paramsString = ($paramsString == '' ? '?' : '&') . $key . '=' . $param;
            }

            return $paramsString;
        }

        return $paramsString;
    }
}