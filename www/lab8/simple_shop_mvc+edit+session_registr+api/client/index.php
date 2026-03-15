<?php
require 'models/Client.php';

$clientModel = new Client();
$clientsResult = $clientModel->getAll();

$clients = [];
while($row = $clientsResult->fetch_assoc()) {
    $clients[] = $row;
}

include 'views/clients.php';
?>
