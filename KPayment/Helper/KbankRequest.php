<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/18/2018
 * Time: 1:56 PM
 */

namespace Marvelic\KPayment\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Marvelic\KPayment\Helper\KbankHash;
use Marvelic\KPayment\Helper\KbankCurrencyCode;

class KbankRequest extends AbstractHelper
{
    private $objConfigSettings;
    private $objKBankHashHelper;
    private $objStoreManagerInterface;
    private $objKBankCurrencyCodeHelper;

    function __construct(
        ScopeConfigInterface $configSettings,
        KBankHash $KBankHash,
        StoreManagerInterface $storeManagerInterface,
        KBankCurrencyCode $KBankCurrencyCode)
    {
        $this->objConfigSettings = $configSettings->getValue('payment/kpayment');
        $this->objKBankHashHelper = $KBankHash;
        $this->objStoreManagerInterface = $storeManagerInterface;
        $this->objKBankCurrencyCodeHelper = $KBankCurrencyCode;
        $this->_configLocaleTimezone = $configSettings->getValue('general/locale/timezone');
    }
    //Declare the Form array to hold the kbank form request.

    private $arrayKBankFormFields = array(
        "MERCHANT2" => "",
        "TERM2" => "",
        "AMOUNT2" => "",
        "URL2" => "",
        "RESURL" => "",
        "IPCUST2" => "",
        "DETAIL2" => "",
        "INVMERCHANT" => "",
        "FILLSPACE" => "",
        "MD5" => ""
    );

    //This function is used to genereate the request for make payment to payment getaway.
    public function KBank_construct_request($parameter, $isLoggedIn) {
        if ($isLoggedIn) {

            //Check stored card is enble by Merchant or not.
            if (array_key_exists("storedCard", $this->objConfigSettings)) {
                if ($this->objConfigSettings['storedCard']) {
                    $enable_store_card = "Y";
                    $this->arrayKBankFormFields["enable_store_card"] = $enable_store_card;

                    if (!empty($parameter['stored_card_unique_id'])) {
                        $this->arrayKBankFormFields["stored_card_unique_id"] = $parameter['stored_card_unique_id'];
                    }
                }
            }
        }

        $this->generateKBankCommonFormFields($parameter);
        $this->arrayKBankFormFields['MD5'] = $this->getCheckSumId();
        $hash_value = $this->objKBankHashHelper->createRequestHashValue($this->arrayKBankFormFields);
        $this->arrayKBankFormFields['CHECKSUM'] = $hash_value;

        $strHtml = '<form name="KBankform" action="' . $this->getPaymentGetwayRedirectUrl() . '" method="post"/>';

        foreach ($this->arrayKBankFormFields as $key => $value) {
            if (!empty($value)) {
                $strHtml .= '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($value) . '">';
            }
        }

        $strHtml .= '</form>';
        $strHtml .= '<script type="text/javascript">';
        $strHtml .= 'document.KBankform.submit()';
        $strHtml .= '</script>';
        return $strHtml;
    }

    //This function is used calculate the amount by selected currency code by merchant in merchant store.
    public function getKBankAmountByCurrencyCode($amount) {

        $exponent = 0;
        $isFounded = false;
        $currency_type = $this->getMerchantSelectedCurrencyCode();

        foreach ($this->objKBankCurrencyCodeHelper->getKBankSupportedCurrenyCode() as $key => $value) {
            if ($value['Num'] === $currency_type) {
                $exponent = $value['Exponent'];
                $isFounded = true;
                break;
            }
        }

        if ($isFounded) {
            if ($exponent == 0 || empty($exponent)) {
                $amount = (int) $amount;
            } else {
                $pg_2c2p_exponent = $this->objKBankCurrencyCodeHelper->getKBankSupportedCurrencyExponents();
                $multi_value = $pg_2c2p_exponent[$exponent];
                $amount = ($amount * $multi_value);
            }
        }

        return str_pad($amount, 12, '0', STR_PAD_LEFT);
    }

    //Creating basic form field request this's required by 2C2P Payment getaway.
    private function generateKBankCommonFormFields($parameter) {

        $this->arrayKBankFormFields["MERCHANT2"] = $this->getMerchantId();
        $this->arrayKBankFormFields["TERM2"] = $this->getTerminalId();
        $this->arrayKBankFormFields["AMOUNT2"] = $this->getAmount($parameter['amount']*100);
        $this->arrayKBankFormFields["URL2"] = $this->getUrl2();
        $this->arrayKBankFormFields["RESURL"] = $this->getResUrl2();
        $this->arrayKBankFormFields["IPCUST2"] = $this->getIPcust();
        $this->arrayKBankFormFields["DETAIL2"] = $parameter['description'];
        $this->arrayKBankFormFields["INVMERCHANT"] = $parameter['invoice_no'];
        $this->arrayKBankFormFields["FILLSPACE"] = $this->objConfigSettings['fillspace'];
    }

    /*Get the selected currency code and converted this's selected currency to number instead of 3 character like 'SGD'. Because 2C2P is accept currency code in Digit only. */
    function getAmount(string $parameter) {
        $result = $parameter;
        while(strlen($result) < 12 ) {
            $result = "0" . $result;
        }

        return $result;
    }

    //Get Payment Getway redirect url to redirect Test URL or Live URL to Kbank PG. It is depending upon the Merchant selected settings in configurations.
    function getPaymentGetwayRedirectUrl() {
        $mode = array_key_exists("mode", $this->objConfigSettings) ? $this->objConfigSettings['mode'] : '';
        if ($mode == 'live') {
            return $this->objConfigSettings['action'];
        } else {
            return $this->objConfigSettings['action_test'];
        }
    }

    //Get the merchant website return URL.
    function getUrl2() {

        $baseUrl = $this->objStoreManagerInterface->getStore()->getBaseUrl();
        return $baseUrl . 'Kpayment/payment/success';
    }

    //Get the merchant website return URL.
    function getResUrl2() {
        $baseUrl = $this->objStoreManagerInterface->getStore()->getBaseUrl();
        return $baseUrl . 'Kpayment/payment/response';
    }

    //Get the merchant id
    function getMerchantId(){
        $mode = array_key_exists("mode",$this->objConfigSettings) ? $this->objConfigSettings['mode'] : '';
        if($mode == 'live'){
            return $this->objConfigSettings['merchant'];
        }else{
            return $this->objConfigSettings['merchant_test'];
        }
    }
    //Get the Terminal ID
    function getTerminalId(){
        $mode = array_key_exists("mode",$this->objConfigSettings) ? $this->objConfigSettings['mode'] : '';
        if($mode == 'live'){
            return $this->objConfigSettings['term'];
        }else{
            return $this->objConfigSettings['term_test'];
        }
    }

    //Get IP config
    function getIPcust(){
        $mode = array_key_exists("mode",$this->objConfigSettings) ? $this->objConfigSettings['mode'] : '';
        if($mode == 'live'){
            return $this->objConfigSettings['ipcust'];
        }else{
            return $this->objConfigSettings['ipcust_test'];
        }
    }

    //Get checksum code
    function getCheckSumId(){
        $mode = array_key_exists("mode",$this->objConfigSettings) ? $this->objConfigSettings['mode'] : '';
        if($mode == 'live'){
            return $this->objConfigSettings['checksum'];
        }else{
            return $this->objConfigSettings['checksum_test'];
        }
    }
}