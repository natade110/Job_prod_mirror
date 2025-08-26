<?php

class wsBankr
{
    var $type; //string
    var $firstName; //string
    var $lastName; //string
    var $idCard; //string
}
class wsBankrResponse
{
    var $return; //BankBean
}
class BankBean
{
    var $userId; //string
    var $userDeptCode; //string
    var $dpdStructureGen; //long
    var $password; //string
    var $username; //string
    var $ipAddr; //string
    var $deptCode; //string
    var $tmpProt; //string
    var $RGaz; //string
    var $uacc; //string
    var $AReqSet; //string
    var $type; //string
    var $close; //string
    var $absReq; //string
    var $dfManage; //string
    var $recvNo; //string
    var $courtName; //string
    var $ACanGaz; //string
    var $BCanSet; //string
    var $lastName; //string
    var $bkrGaz; //string
    var $status; //string
    var $redCase; //string
    var $bkrProt; //string
    var $BCanGaz; //string
    var $absGaz; //string
    var $reBkr; //string
    var $CBkr; //string
    var $dfManageEjc; //string
    var $SBkr; //string
    var $absDue; //string
    var $absEjc; //string
    var $ptName; //string
    var $tmpEjcGaz; //string
    var $result; //string
    var $absEjcGaz; //string
    var $ADueSet; //string
    var $ACouSet; //string
    var $dfId; //string
    var $CGaz; //string
    var $ASetGaz; //string
    var $blackCase; //string
    var $firstName; //string
    var $ACanSet; //string
    var $idCard; //string
    var $tmpEjc; //string
    var $tmpGaz; //string
    var $BDueSet; //string
    var $absProt; //string
    var $BCouSet; //string
    var $BSetGaz; //string
    var $RBkr; //string
    var $BReqSet; //string
    var $dfName; //string
}
class moomin
{
    var $soapClient;
    
    private static $classmap = array('wsBankr' => 'wsBankr', 'wsBankrResponse' => 'wsBankrResponse', 'BankBean' => 'BankBean');
    
    function __construct($url = 'http://ledgdxp.led.go.th/gdxEJB/GdxWebServiceBankr?wsdl')
    {
        $this->soapClient = new SoapClient($url, array(
            "classmap" => self::$classmap,
            "trace" => true,
            "exceptions" => true
        ));
    }
    
    function wsBankr($wsBankr)
    {
        
        $wsBankrResponse = $this->soapClient->wsBankr($wsBankr);
        return $wsBankrResponse;
        
    }
}


?>
