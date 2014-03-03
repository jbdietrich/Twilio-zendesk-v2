<?php

require 'vendor/autoload.php';
use Zendesk\API\Client as ZendeskAPI;

ini_set('display_errors', 0);
error_reporting(0);

$subdomain = "";
$username = "";
$token = ""; // replace this with your token
// $password = "123456";
$field_id = ""

if (isset($_POST)){
    $from    = isset($_POST['From']) ?
                $_POST['From'] : 'bad phone number';
    $body    = isset($_POST['Body']) ?
                $_POST['Body'] : 'An error occured from this number: '.$from;

    $client = new ZendeskAPI($subdomain, $username);
    $client->setAuth('token', $token); // set either token or password

    $queryString = "type:ticket status<closed sort:desc order_by:updated_at fieldvalue:".$from;
    $result = $client->search(array('query' => $queryString));

    if ($result->count > 0){
        $ticket_id = $result->results[0]->id;
        $result = $client->ticket($ticket_id)->update(array (
            'comment' => array (
                'body'   => $body,
                'public' => true,
            ),
        ));
    } else {
        $result = $client->tickets()->create(array (
            'comment' => array (
                'body'   => $body,
                'public' => true
            ),
            'custom_fields' => array (
                'id'  => $field_id,
                'value' => $from
            ),
        ));
    }
}

header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
