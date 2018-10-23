<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/18/2018
 * Time: 10:50 AM
 */

namespace Wiki\KPayment\Helper;


class KbankHash
{
    private $params, $hashValue;

    protected $_logger;

    public function __construct(\Psr\Log\LoggerInterface $logger){
        $this->_logger = $logger;
    }
    // This function is used to check the hash value is valid or not .
    public function isValidHashValue($reqHash,$resHash){
        $this->_logger->info('My generated hash', ['value' => $resHash]); // write log
        //Return hash value result.
        if (strcasecmp($reqHash, $resHash) == 0) {
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
        if(array_key_exists('RESPURL',$parameter)) {
            if(!empty($parameter['RESPURL']))
                $this->hashValue .= $parameter['RESPURL'];
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

    //Response massage
    public function getResponseDesc($code){
        switch ($code){
            case 01 :
                return "Refer to card issuer - Give cardholder contacts issuer bank";
                break;
            case 03 :
                return "Invalid Merchant ID - Please contact KBank";
                break;
            case 05 :
                return "Do not honor - Cardholder input invalid card information. Ex. Expirydate, CVV2 or card number. Give cardholder contacts issuer bank.";
                break;
            case 12 :
                return "Invalid transaction - Please contact KBank";
                break;
            case 13 :
                return "Invalid Amount Payment amount must more than 0.1";
                break;
            case 14 :
                return "Invalid Card Number - Please check all digits of card no.";
                break;
            case 17 :
                return "Customer Cancellation - Customers click at cancel button in payment page when they make transaction. Customers have to make new payment transaction.";
                break;
            case 19 :
                return "Re-enter transaction Duplicate payment. - Please contact KBank";
                break;
            case 30 :
                return "Format Error Transaction format error. - Please contact KBank";
                break;
            case 41 :
                return "Lost Card – Pick up - Lost Card and Cardholder give up.";
                break;
            case 43 :
                return "Stolen Card – Pick up - Stolen Card and Cardholder give up";
                break;
            case 50 :
                return "Invalid Payment Condition - Ex. Session time out or invalid VbV Password : ask cardholders to try ma again and complete transaction within 15 minutes with correct card information.";
                break;
            case 51 :
                return "Insufficient Funds - Not enough credit limit to pay. Please contact issuer";
                break;
            case 54 :
                return "Expired Card - Cardholder key in invalid expiry date";
                break;
            case 58 :
                return "Transaction not Permitted to Terminal - Issuer does not allow to pay with debit card (Visa Electron, Mastercard Electron)";
                break;
            case 91 :
                return "Issuer or Switch is Inoperative - Issuer system is not available to authorize payment";
                break;
            case 94 :
                return "Duplicate Transaction - Please inform KBank to investigate";
                break;
            case 96 :
                return "System Malfunction - Issuer bank system can not give a service";
                break;
            case "xx" :
                return "Transaction Timeout - Can not receive response code from issuer with in the time limit";
                break;
            default:
                {
                    return "Unknown Issue";
                    break;
                }
        }
    }

    public function getPmgwresp($code){
        $array = array();
        $array['trans_code'] = substr($code,0,4);
        $array['merchant_id'] = substr($code,4,15);
        $array['terminal_id'] = substr($code,19,8);
        $array['shop_no'] = substr($code,27,2);
        $array['currency_code'] = substr($code,29,3);
        $array['invoice_no'] = substr($code,32,12);
        $array['date'] = substr($code,44,8);
        $array['time'] = substr($code,52,6);
        $array['card_no'] = substr($code,58,19);
        $array['expired_date'] = substr($code,77,4);
        $array['cvv2_cvc2'] = substr($code,81,4);
        $array['trans_amount'] = substr($code,85,12);
        $array['response_code'] = substr($code,97,2);
        $array['approval_code'] = substr($code,99,6);
        $array['card_type'] = substr($code,105,3);
        $array['fx_rate'] = substr($code,168,20);
        $array['thb_amount'] = substr($code,188,20);
        $array['customer_email'] = substr($code,208,100);
        $array['description'] = substr($code,308,150);
        $array['player_ip_address'] = substr($code,458,18);
        $array['warning_light'] = substr($code,476,1);
        $array['selected_bank'] = substr($code,477,60);
        $array['issuer_bank'] = substr($code,537,60);
        $array['selected_country'] = substr($code,597,45);
        $array['ip_country'] = substr($code,642,45);
        $array['issuer_country'] = substr($code,687,45);
        $array['eci'] = substr($code,732,4);
        $array['xid'] = substr($code,736,40);
        $array['cavv'] = substr($code,776,40);
        return $array;
    }
}