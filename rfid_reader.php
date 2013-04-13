#!/usr/bin/php
<?php

// Include the class to read the serial line.
include ("php_serial.class.php");  

// Let's start the class
$serial = new phpSerial;

$serial->deviceSet("/dev/ttyAMA0");
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
// We can change the baud rate, parity, length, stop bits, flow control
#$serial->confFlowControl("none");

// Check if we can open the serial line.  Otherwise die.
if(!$serial->deviceOpen()) die("unable to open device");

stream_set_timeout($serial->_dHandle, 10);

$rfid_key = FALSE;

// Start the loop to keep checking the
while(!$rfid_key)
{
    $read = $serial->readPort();

    // Array to store eachvalue of the RFID tag
    $ascii_read  = array();

    for($i = 0; $i < strlen($read); $i++)
    {
        $ascii_read[] = ord($read[$i]);
        if(count($ascii_read) == 14 && $ascii_read[0] == 2 && $ascii_read[13] == 3)
        {
            $rfid_key = implode("", $ascii_read);
            break;  
        } 
    }

    // If the key is empty then sleep for 1 second.
    if(!$rfid_key)
    {
	time_nanosleep(0, 20000000);
    }
}

print_r($rfid_key);
print "\n\n";
