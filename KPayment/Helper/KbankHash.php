<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/18/2018
 * Time: 10:50 AM
 */

namespace Marvelic\KPayment\Helper;


class KbankHash
{
    private $params, $hashValue;

    protected $_logger;

    public function __construct(\Psr\Log\LoggerInterface $logger){
        $this->_logger = $logger;
    }
    // This function is used to check the hash value is valid or not .
    public function isValidHashValue($parameter){
        if (array_key_exists('MERCHANT2', $parameter)) {
            $this->params .= $parameter['MERCHANT2'];
        }
        if (array_key_exists('TERM2', $parameter)) {
            $this->params .= $parameter['TERM2'];
        }
        if (array_key_exists('AMOUNT2', $parameter)) {
            $this->params .= $parameter['AMOUNT2'];
        }
        if (array_key_exists('URL2', $parameter)) {
            $this->params .= $parameter['URL2'];
        }
        if (array_key_exists('RESURL', $parameter)) {
            $this->params .= $parameter['RESURL'];
        }
        if (array_key_exists('IPCUST2', $parameter)) {
            $this->params .= $parameter['IPCUST2'];
        }
        if (array_key_exists('DETAIL2', $parameter)) {
            $this->params .= $parameter['DETAIL2'];
        }
        if (array_key_exists('INVMERCHANT', $parameter)) {
            $this->params .= $parameter['INVMERCHANT'];
        }
        if (array_key_exists('FILLSPACE', $parameter)) {
            $this->params .= $parameter['FILLSPACE'];
        }
        if (array_key_exists('SHOPID', $parameter)) {
            $this->params .= $parameter['SHOPID'];
        }
        if (array_key_exists('PAYTERM2', $parameter)) {
            $this->params .= $parameter['PAYTERM2'];
        }
        if (array_key_exists('MD5', $parameter)) {
            $this->params .= $parameter['MD5'];
        }
        //Generate hash based on hash alogorithm.
        $hash = md5($this->params);
        $this->_logger->info('My generated hash', ['value' => $hash]); // write log
        //Return hash value result.
        if (strcasecmp($hash, $parameter['hash_value']) == 0) {
            return true;
        }

        return false;
    }

    // This function is used to generate the hash value for the current Merchant user request.
    public function createRequestHashValue($parameter){
        if(array_key_exists('MERCHANT2',$parameter)) {
            if(!empty($parameter['MERCHANT2']))
                $this->hashValue .= $parameter['MERCHANT2'];
        }
        if(array_key_exists('TERM2',$parameter)) {
            if(!empty($parameter['TERM2']))
                $this->hashValue .= $parameter['TERM2'];
        }
        if(array_key_exists('AMOUNT2',$parameter)) {
            if(!empty($parameter['AMOUNT2']))
                $this->hashValue .= $parameter['AMOUNT2'];
        }
        if(array_key_exists('URL2',$parameter)) {
            if(!empty($parameter['URL2']))
                $this->hashValue .= $parameter['URL2'];
        }
        if(array_key_exists('RESURL',$parameter)) {
            if(!empty($parameter['RESURL']))
                $this->hashValue .= $parameter['RESURL'];
        }
        if(array_key_exists('IPCUST2',$parameter)) {
            if(!empty($parameter['IPCUST2']))
                $this->hashValue .= $parameter['IPCUST2'];
        }
        if(array_key_exists('DETAIL2',$parameter)) {
            if(!empty($parameter['DETAIL2']))
                $this->hashValue .= $parameter['DETAIL2'];
        }
        if(array_key_exists('INVMERCHANT',$parameter)) {
            if(!empty($parameter['INVMERCHANT']))
                $this->hashValue .= $parameter['INVMERCHANT'];
        }
        if(array_key_exists('FILLSPACE',$parameter)) {
            if(!empty($parameter['FILLSPACE']))
                $this->hashValue .= $parameter['FILLSPACE'];
        }
        if(array_key_exists('MD5',$parameter)) {
            if(!empty($parameter['MD5']))
                $this->hashValue .= $parameter['MD5'];
        }
        //Return hash value result.
        return md5($this->hashValue);
    }
}