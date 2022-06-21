<?php
require  'vendor/autoload.php';
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$values = [
    'first_name' => 'John',
    'last_name'  => 'Smith',
    'email'      => 'johnsmith@email.com',
    'phone'      => '+11231231234'
];
$dbHost     = "127.0.0.1";
$dbUsername = "root";
$dbPassword = '';
$dbName     = "phpsheets";

$select = "SELECT * FROM users WHERE email='".$values['email']."'";

$table = "CREATE TABLE users
            (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(30) NOT NULL,
            email VARCHAR(60) NOT NULL UNIQUE,
            phone VARCHAR(60) NOT NULL)";

$createUser = "INSERT INTO users 
                (first_name, last_name, email, phone)
                VALUES 
                ('".$values['first_name']."', '".$values['last_name']."', '".$values['email']."', '".$values['phone']."')";

// DB configuration
try{
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    if($conn->connect_error){
        echo ("Failed to connect with MySQL: " . $conn->connect_error);
    }else {
        $db = $conn;
        echo "Connected successfully";
    }
}
catch (PDOException $e) {
    echo "Error in connection " . $e->getMessage();
}

$client = new \Google_Client();
$client->setApplicationName('Google Sheets and PHP');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__ . '/credentials.json');
$service = new Google_Service_Sheets($client);

$spreadsheetId = "1hI_HLok7TggbwMizpdbt61SoQTbioQMST6utLJOsaSg"; //spreadsheet ID which is present in URL
$range = "Sheet1"; //Sheet name

try{
    $db->query($createUser);
    $result = $db->query($select);
    $users = mysqli_fetch_object($result);

    $sheetValues = [[$users->id, $users->first_name, $users->last_name, $users->email, $users->phone]];

    $body = new Google_Service_Sheets_ValueRange(
        [
        'values' => $sheetValues
        ]
    );
    $params = [
        'valueInputOption' => 'RAW'
    ];
    $insert = [
            'insertDataOption' => 'INSERT_ROWS'
    ];
    $result = $service->spreadsheets_values->append(
        $spreadsheetId,
        $range,
        $body,
        $params,
        $insert
    );
}
catch (\Exception $e) {
    echo "Something went wrong " . $e->getMessage();
}
