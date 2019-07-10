<?php
header('Content-Type: text/html; charset=utf-8');
//Создание
function createLead($xml, $token) {        
    $postData = array();
    $postData['authtoken'] = $token;
    $postData['scope'] = 'crmapi';
    $postData['newFormat'] = '2';

    $url = 'https://crm.zoho.eu/crm/private/xml/Leads/insertRecords';
    $url = $url . '?authtoken='.$postData['authtoken'].'&scope=crmapi';
    $postData['xmlData'] = $xml;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
// Поиск
function searchLead($phone, $token) {
    $postData = array();
    $postData['authtoken'] = $token;
    $postData['scope'] = 'crmapi';
    $postData['newFormat'] = '2';
    $postData['criteria'] = '(Mobile:'.$phone.')';
    $url = 'https://crm.zoho.eu/crm/private/xml/Leads/searchRecords?authtoken='.$token.'&scope=crmapi&criteria=(((Phone:'.$phone.')))';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//Конвертирование
function convertLead($xml, $token, $leadId){
    $postData = array();
    $postData['authtoken'] = $token;
    $postData['scope'] = 'crmapi';
    $postData['newFormat'] = '2';
    $postData['xmlData'] = $xml;
    $postData['leadId'] = $leadId;
    $url = 'https://crm.zoho.eu/crm/private/xml/Leads/convertLead?authtoken='.$token.'&scope=crmapi&leadId='.$leadId.'&xmlData='.$xml;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function GetBetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}


$token = 'b154cbaa66e35084522bea827a74b93b';
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$sum = $_POST['sum'];
$lastName = $_POST['lastName'];
$from = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
if ($name == '' || $phone == '' || $email == '' || $sum == '' || $lastName == '') {
    echo 'Заполните все поля';
    die;
}
$xml = '
    <Leads>
    <row no="1">
        <FL val="Company">'.$from.'</FL>
        <FL val="First Name">'.$name.'</FL>
        <FL val="Last Name">'.$lastName.'</FL>
        <FL val="Email">'.$email.'</FL>
        <FL val="Phone">'.$phone.'</FL>
        <FL val="Mobile">'.$sum.'</FL>
    </row>
    </Leads>
';

$find  = searchLead($phone, $token);
$leadId = GetBetween($find,'val="LEADID">','</');
if ($leadId) {
    convertLead($xml, $token, $leadId);
    echo "succsess converted";
}else {
    createLead($xml, $token);
    echo 'success created';
}
?>