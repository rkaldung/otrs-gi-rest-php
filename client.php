#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

Unirest\Request::defaultHeader("Accept", "application/json");
Unirest\Request::defaultHeader("Content-Type", "application/json");
Unirest\Request::verifyPeer(true);

$FQDN = 'FIXME';
$WebServiceName = 'GenericTicketConnectorREST';
$BaseURL = "https://$FQDN/otrs/nph-genericinterface.pl/Webservice/$WebServiceName";
$headers = [];
$body = json_encode(
    [
        "UserLogin" => "FIXME",
        "Password"  => "FIXME",
    ]
);

/**
 * SessionCreate
 *
 * http://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Session/SessionCreate.pm.html
 */
$response = Unirest\Request::post($BaseURL."/Session", $headers, $body);
if (!$response->body->SessionID) {
    print "No SessionID returned \n";
    exit(1);
}
$SessionID = $response->body->SessionID;
print "Your SessionID is $SessionID\n";


/**
 * TicketCreate
 *
 * https://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Ticket/TicketCreate.pm.html
 */
$attachment = file_get_contents("README.md");
$body = json_encode([
        'SessionID' => $SessionID,
        'Ticket' => [
            'Title' => 'Example ticket from PHP',
            'Queue' => 'Postmaster',
            'CustomerUser' => 'info@znuny.com',
            'State' => 'new',
            'Priority' => '3 normal',
            'OwnerID' => 1,
        ],
        'Article' =>[
            'CommunicationChannel' => 'Email',  
            'ArticleTypeID' => 1,
            'SenderTypeID' => 1,
            'Subject' => 'Example',
            'Body' => 'This is a GenericInterface example.',
            'ContentType' => 'text/plain; charset=utf8',
            'Charset' => 'utf8',
            'MimeType' => 'text/plain',
            'From' => 'info@znuny.com',
        ],
        'Attachment' => [
            'Content' => base64_encode($attachment),
            'ContentType' => 'text/plain',
            'Filename' => 'README.md'
        ],
    ]
);

$response = Unirest\Request::post($BaseURL."/Ticket", $headers, $body);
if ( $response->body->Error ) {
    $ErrorCode = $response->body->Error->ErrorCode;
    $ErrorMessage = $response->body->Error->ErrorMessage;
    print "ErrorCode $ErrorCode\n";
    print "ErrorMessage $ErrorMessage\n";
    exit(1);
}
$TicketNumber = $response->body->TicketNumber;
$TicketID = $response->body->TicketID;
$ArticleID = $response->body->ArticleID;

print "\nThe ticket $TicketNumber was created. Check it via https://$FQDN/otrs/index.pl?Action=AgentTicketZoom;TicketID=$TicketID\n";


/**
 * TicketUpdate
 *
 * http://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Ticket/TicketUpdate.pm.html
 */
$param = json_encode([
    'SessionID' => $SessionID,
    'Ticket' => [
        'Queue' => 'Raw',
        'State' => 'open'
    ]
]);
$response = Unirest\Request::patch($BaseURL."/Ticket/${TicketID}", $headers, $param);
if ( $response->body->Error ) {
    $ErrorCode = $response->body->Error->ErrorCode;
    $ErrorMessage = $response->body->Error->ErrorMessage;
    print "ErrorCode $ErrorCode\n";
    print "ErrorMessage $ErrorMessage\n";
    exit(1);
}
print "\nThe ticket was moved to the queue 'Raw' and the state changed to 'open'\n";

/**
 * TicketGet
 *
 * http://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Ticket/TicketGet.pm.html
 */
$param = [
    'SessionID' => $SessionID,
];
$response = Unirest\Request::get($BaseURL."/Ticket/${TicketID}?Extended=1", $headers, $param);
if ( $response->body->Error ) {
    $ErrorCode = $response->body->Error->ErrorCode;
    $ErrorMessage = $response->body->Error->ErrorMessage;
    print "ErrorCode $ErrorCode\n";
    print "ErrorMessage $ErrorMessage\n";
    exit(1);
}
$TicketData = $response->body->Ticket[0];
print "\nThe ticket data:\n";
foreach($TicketData as $key => $value) {
    if ($value) {
        print "$key: $value\n";    
    }
}

/**
 * TicketSearch
 *
 * http://doc.otrs.com/doc/api/otrs/6.0/Perl/Kernel/GenericInterface/Operation/Ticket/TicketSearch.pm.html
 */
$param = [
    'SessionID' => $SessionID,
    'StateType' => ['new', 'open'],
    'TicketCreateTimeNewerMinutes' => 15,
];
$response = Unirest\Request::get($BaseURL."/Ticket", $headers, $param);
if ( $response->body->Error ) {
    $ErrorCode = $response->body->Error->ErrorCode;
    $ErrorMessage = $response->body->Error->ErrorMessage;
    print "ErrorCode $ErrorCode\n";
    print "ErrorMessage $ErrorMessage\n";
    exit(1);
}
$Count = count($response->body->TicketID);
print "\nThere is/are $Count ticket(s) with the state new or open, created during the last 15 minutes.\n";
$TicketIDs = $response->body->TicketID;
foreach($TicketIDs as $TicketID) {
    print "https://$FQDN/otrs/index.pl?Action=AgentTicketZoom;TicketID=$TicketID \n";
} 
