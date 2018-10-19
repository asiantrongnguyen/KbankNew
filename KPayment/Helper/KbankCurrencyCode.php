<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/18/2018
 * Time: 10:19 AM
 */

namespace Marvelic\KPayment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
class KbankCurrencyCode extends AbstractHelper
{
    public function getKbankSupportedCurrenyCode(){
        return array(
            "THB" => array("Code"=>"764","No" => "1"),
            "USD" => array("Code"=>"840","No" => "2"),
            "EUR" => array("Code"=>"978","No" => "3"),
            "JPY" => array("Code"=>"392","No" => "4"),
            "GBP" => array("Code"=>"826","No" => "5"),
            "AUD" => array("Code"=>"036","No" => "6"),
            "NZD" => array("Code"=>"554","No" => "7"),
            "HKD" => array("Code"=>"344","No" => "8"),
            "SGD" => array("Code"=>"702","No" => "9"),
            "CHF" => array("Code"=>"756","No" => "10"),
            "INR" => array("Code"=>"356","No" => "11"),
            "NOK" => array("Code"=>"578","No" => "12"),
            "DKK" => array("Code"=>"208","No" => "13"),
            "SEK" => array("Code"=>"752","No" => "14"),
            "CAD" => array("Code"=>"124","No" => "15"),
            "MYR" => array("Code"=>"458","No" => "16"),
            "CNY" => array("Code"=>"156","No" => "17"),
            "TWD" => array("Code"=>"901","No" => "18"),
            "MOD" => array("Code"=>"446","No" => "19"),
            "BND" => array("Code"=>"096","No" => "20"),
            "AED" => array("Code"=>"784","No" => "21"),
            "LKR" => array("Code"=>"144","No" => "22"),
            "BDT" => array("Code"=>"050","No" => "23"),
            "SAR" => array("Code"=>"682","No" => "24"),
            "NPR" => array("Code"=>"524","No" => "25"),
            "PKR" => array("Code"=>"586","No" => "26"),
            "ZAR" => array("Code"=>"710","No" => "27"),
            "PHP" => array("Code"=>"608","No" => "28"),
            "QAR" => array("Code"=>"634","No" => "29"),
            "VND" => array("Code"=>"704","No" => "30"),
            "OMR" => array("Code"=>"512","No" => "31"),
            "RUB" => array("Code"=>"643","No" => "32"),
            "KRW" => array("Code"=>"410","No" => "33"),
            "IDR" => array("Code"=>"360","No" => "34"),
            "KWD" => array("Code"=>"414","No" => "35"),
            "BHD" => array("Code"=>"048","No" => "36")
        );
    }


}