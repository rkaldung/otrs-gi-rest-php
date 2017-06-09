#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

Unirest\Request::defaultHeader("Accept", "application/json");
Unirest\Request::defaultHeader("Content-Type", "application/json");
Unirest\Request::verifyPeer(true);

$BaseURL = 'https://FQDN/otrs/nph-genericinterface.pl/Webservice/GenericTicketConnectorREST';
$headers = [];
$body = json_encode(
    [
        "UserLogin" => "REPLACEME",
        "Password"  => "REPLACEME",
    ]
);

/**
 * SessionCreate
 *
 * http://doc.otrs.com/doc/api/otrs/stable/Perl/Kernel/GenericInterface/Operation/Session/SessionCreate.pm.html
 */
$response = Unirest\Request::post($BaseURL."/Session", $headers, $body);
if (!$response->body->SessionID) {
    print "No SessionID returnd \n";
    exit(1);
}
$SessionID = $response->body->SessionID;


/**
 * TicketCreate
 *
 * http://doc.otrs.com/doc/api/otrs/stable/Perl/Kernel/GenericInterface/Operation/Ticket/TicketCreate.pm.html
 */
$attachment = file_get_contents("example.bin");
$body = json_encode([
        'SessionID' => $SessionID,
        'Ticket' => [
            'Title' => 'Example ticket',
            'Queue' => 'Postmaster',
            'CustomerUser' => 'info@znuny.com',
            'State' => 'new',
            'Priority' => '3 normal',
            'OwnerID' => 1,
            'TypeID' => 1,
        ],
        'Article' =>[
            'ArticleSend' => 1,
            'ArticleTypeID' => 1,
            'SenderTypeID' => 1,
            'Subject' => 'Example',
            'Body' => 'This is a GenericInterface example.',
            'ContentType' => 'text/plain; charset=utf8',
            'Charset' => 'utf8',
            'MimeType' => 'text/plain',
            'To' => 'info@znuny.com',
        ]
    ]
);

$response = Unirest\Request::post($BaseURL."/Ticket", $headers, $body);
$TicketNumber = $response->body->TicketNumber;
$TicketID = $response->body->TicketID;

/**
 * TicketUpdate
 *
 * http://doc.otrs.com/doc/api/otrs/stable/Perl/Kernel/GenericInterface/Operation/Ticket/TicketUpdate.pm.html
 */
$param = json_encode([
    'SessionID' => $SessionID,
    'Ticket' => [
        'Queue' => 'Raw',
        'State' => 'open'
    ]
]);
$response = Unirest\Request::patch($BaseURL."/Ticket/${TicketID}", $headers, $param);


/**
 * TicketGet
 *
 * http://doc.otrs.com/doc/api/otrs/stable/Perl/Kernel/GenericInterface/Operation/Ticket/TicketGet.pm.html
 */
$param = [
    'SessionID' => $SessionID,
];
$response = Unirest\Request::get($BaseURL."/Ticket/${TicketID}", $headers, $param);


/**
 * TicketSearch
 *
 * http://doc.otrs.com/doc/api/otrs/stable/Perl/Kernel/GenericInterface/Operation/Ticket/TicketSearch.pm.html
 */
$param = [
    'SessionID' => $SessionID,
    'StateType' => ['new', 'open'],
    'TicketCreateTimeOlderMinutes' => 5,
];
$response = Unirest\Request::get($BaseURL."/Ticket", $headers, $param);
var_dump($response);
