<?php
error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors','On');

error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors','On');
// LDAP variables
$ldaphost = "172.24.10.3";  // your ldap servers
$ldapport = 389;                 // your ldap server's port number

// using ldap bind
$ldaprdn  = 'test@dpo.go.th';     // ldap rdn or dn
//$ldaprdn  = 'DPO\kittipon.k';
$dn ="OU=DPOUsers,DC=dpo,DC=go,DC=th";

$ldappass = '1234567';  // associated password
//$ldappass = '0871859271';  // associated password


// connect to ldap server
$ldapconn = ldap_connect($ldaphost, $ldapport);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
if ($ldapconn) {
    
    //echo 'binding to ldap server<br>';
    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
    if ($ldapbind) {
        echo "User " . $ldaprdn. " : LDAP bind successful...<br>";
    } else {
        echo "User " . $ldaprdn. " : LDAP bind failed...";
    }
    //exit;
    //echo $ldapbind = ldap_bind($ldapconn, null, null);
    $res =  ldap_search($ldapconn, $dn, "(userPrincipalName=$ldaprdn)");
    
    $total_record = ldap_count_entries($ldapconn, $res);
    $returned = ldap_get_entries($ldapconn, $res);

    if ($total_record > 0) {
        echo "<pre>";
        print_r($returned);
    }
    //ldap_count_entries($ldapconn, $res);exit;
    if (!$res) {
        $errorNo = ldap_errno($ldapconn);
        echo $ldapconn . " LDAP-Errno: " . $errorNo . "<br />\n";
        echo 'error desc : '.ldap_err2str ($errorNo);
        print_r($res);
        //die("Argh!<br />\n");
        exit;
    }
    $info = ldap_get_entries($ldapconn, $res);
    echo $info["count"] . " matching entries.<br />\n";

    // verify binding
    
}



error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors','On');
include 'Net/LDAP2.php';

$dn ="CN=Users,DC=dpo,DC=go,DC=th";

// The configuration array:
$config = array (
    'binddn'    => 'test@dpo.go.th',
    'bindpw'    => '1234567',
    'basedn'    => 'OU=DPOUsers,DC=dpo,DC=go,DC=th',
    'host'      => '172.24.10.3',
    'port'      => 389
);

// Connecting using the configuration:
$ldap = Net_LDAP2::connect($config);
// print_r($ldap);
// Testing for connection error
if (PEAR::isError($ldap)) {
    die('Could not connect to LDAP-server: '.$ldap->getMessage());
}

$options = array(
    'scope' => 'sub',
    'attributes' => array("displayname", "mail", "samaccountname")
);
$search = $ldap->search($dn, '(objectClass=*)', $options);
if (PEAR::isError($search)) {
    die($search->getMessage() . "\n");
}

// Say how many entries we have found:
echo "Found " . $search->count() . " entries!";



/*
$ldaprdn  = 'test@dpo.go.th';
$ldappass = '1234567';
$ldaphost = "172.24.10.3";  // your ldap servers
$ldapport = 389;                 // your ldap server's port number
$dn ="CN=DPOUser, DC=dpo, DC=go, DC=th";

$ldapconn = ldap_connect($ldaphost, $ldapport);

ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

print_r($ldapbind);

$search_filter = '(&(objectCategory=person)(samaccountname=*))';
$attributes = array();
$attributes[] = 'givenname';
$attributes[] = 'mail';
$attributes[] = 'samaccountname';
$attributes[] = 'sn';
$result = ldap_search($ldapconn, $dn, $search_filter, $attributes);

    $entries = ldap_get_entries($ldapconn, $result);
    for ($x=0; $x<$entries['count']; $x++){
        if (!empty($entries[$x]['givenname'][0]) &&
             !empty($entries[$x]['mail'][0]) &&
             !empty($entries[$x]['samaccountname'][0]) &&
             !empty($entries[$x]['sn'][0]) &&
             'Shop' !== $entries[$x]['sn'][0] &&
             'Account' !== $entries[$x]['sn'][0]){
            $ad_users[strtoupper(trim($entries[$x]['samaccountname'][0]))] = array('email' => strtolower(trim($entries[$x]['mail'][0])),'first_name' => trim($entries[$x]['givenname'][0]),'last_name' => trim($entries[$x]['sn'][0]));
        }
    }

    echo $message .= "Retrieved ". count($ad_users) ." Active Directory users\n";
*/
?>