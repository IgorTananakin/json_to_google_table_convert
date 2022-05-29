<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Подключаем клиент Google таблиц
require_once __DIR__ . '/vendor/autoload.php';

// Наш ключ доступа к сервисному аккаунту
$googleAccountKeyFilePath = __DIR__ . '/service_key.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);

// Создаем новый клиент
$client = new Google_Client();
// Устанавливаем полномочия
$client->useApplicationDefaultCredentials();

// Добавляем область доступа к чтению, редактированию, созданию и удалению таблиц
$client->addScope(['https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/spreadsheets']);




$service = new Google_Service_Sheets($client);

// ID таблицы
$spreadsheetId = '1Uchevo9xfyq0-reRsiH9scTYU_7u8aUHmYA1TjyHPHo';

$response = $service->spreadsheets->get($spreadsheetId);

// Идентификатор таблицы
var_dump($response->spreadsheetId);

// URL страницы
var_dump($response->spreadsheetUrl);

// Получение свойств таблицы
$spreadsheetProperties = $response->getProperties();

// Имя таблицы
var_dump($spreadsheetProperties->title);

// Обход всех листов
foreach($response->getSheets() as $sheet) {

        // Получаем свойства листа
        $sheetProperties = $sheet->getProperties();
        // Идентификатор листа
        var_dump($sheetProperties->index);
        // Имя листа
        var_dump($sheetProperties->title);
}


// Объект - свойства таблицы
$SpreadsheetProperties = new Google_Service_Sheets_SpreadsheetProperties();
// Название таблицы
$SpreadsheetProperties->setTitle('NewSpreadsheet');
// Объект - таблица
$Spreadsheet = new Google_Service_Sheets_Spreadsheet();
$Spreadsheet->setProperties($SpreadsheetProperties);
// Делаем запрос
$response = $service->spreadsheets->create($Spreadsheet);

// Выводим идентификатор и url новой таблицы
var_dump($response->spreadsheetId);
var_dump($response->spreadsheetUrl);

// Объект - диск
$Drive = new Google_Service_Drive($client);
// Объект - разрешения диска
$DrivePermisson = new Google_Service_Drive_Permission();
// Тип разрешения
$DrivePermisson->setType('user');
// Указываем свою почту
$DrivePermisson->setEmailAddress('tananakinigor98@gmail.com');
// Права на редактирование
$DrivePermisson->setRole('writer');
// Выполняем запрос с нашим spreadsheetId, полученным в предыдущем примере
$response = $Drive->permissions->create('1Uchevo9xfyq0-reRsiH9scTYU_7u8aUHmYA1TjyHPHo', $DrivePermisson);

$Drive = new Google_Service_Drive($client);
$DrivePermissions = $Drive->permissions->listPermissions($spreadsheetId);

foreach ($DrivePermissions as $key => $value) {
    $role = $value->role;
    
    var_dump($role);
}

// Диапазон, в котором мы определяем заполненные данные. Например, если указать диапазон A1:A10
// и если ячейка A2 ячейка будет пустая, то новое значение запишется в строку, начиная с A2.
// Поэтому лучше перестраховаться и указать диапазон побольше:
$range = 'A1:Z';
// Данные для добавления
$values = [
  [
      "Дата матча", "ID матча", "ID домашней команды", "Название домашней команды", "ID соперника (команда)", "Название соперника (команда)",
      "Домашний счёт", "Соперника счёт", "1 период home", "1 период away", "2 период home", "2 период away", "3 период home", "3 период away"
],
];
// Объект - диапазон значений
$ValueRange = new Google_Service_Sheets_ValueRange();
// Устанавливаем наши данные
$ValueRange->setValues($values);
// Указываем в опциях обрабатывать пользовательские данные
$options = ['valueInputOption' => 'USER_ENTERED'];
// Добавляем наши значения в последнюю строку (где в диапазоне A1:Z все ячейки пустые)
$service->spreadsheets_values->append('1Uchevo9xfyq0-reRsiH9scTYU_7u8aUHmYA1TjyHPHo', $range, $ValueRange, $options);




// // Экспорт в Excel
// // 25.03.2022
// $division = $_GET["division"];
// $date = $_GET["date"];
// $format = $_GET["format"];



echo "<br>";
$jsondata2 = file_get_contents("https://scout.bigsports.ru/wp-json/scout_calendar/v1/league=17910,17911,17912,17913,17914,19276,19277,19278,19279/date=09.03.2022/status=4");
//$jsondata2 = json_decode("https://scout.bigsports.ru/wp-json/scout_calendar/v1/league=17910,17911,17912,17913,17914,19276,19277,19278,19279/date=09.03.2022/status=4",true);
$jsonDecoded = json_decode($jsondata2, true); // add true, will handle as associative array

$excelData = [];
if (is_array($jsonDecoded)) {
    foreach ($jsonDecoded as $line) {

        foreach ($line as $key => $value) {

            $lineData = array($line[$key]["match_date"], $line[$key]["model"]["match_id"], $line[$key]["homeTeamID"], strip_tags($line[$key]["homePartName"]),
							  $line[$key]["awayTeamID"], strip_tags($line[$key]["awayPartName"]), $line[$key]["home_score"], $line[$key]["away_score"],
                //$arrays["ScoreHome"] + $arrays["ScoreAway"],
                $line[$key]["stages"]["2"]["0"], $line[$key]["stages"]["2"]["1"], $line[$key]["stages"]["3"]["0"], $line[$key]["stages"]["3"]["1"], $line[$key]["stages"]["4"]["0"], $line[$key]["stages"]["4"]["1"], 
						
					    
            );
            var_dump($lineData);
            echo " заняслось <br>";
            $excelData = array_merge($excelData,$lineData);

            // Диапазон, в котором мы определяем заполненные данные. Например, если указать диапазон A1:A10
            // и если ячейка A2 ячейка будет пустая, то новое значение запишется в строку, начиная с A2.
            // Поэтому лучше перестраховаться и указать диапазон побольше:
            $range = 'A1:Z';
            // Данные для добавления
            $values = [
            [
                $line[$key]["match_date"], $line[$key]["model"]["match_id"], $line[$key]["homeTeamID"], strip_tags($line[$key]["homePartName"]), 
                $line[$key]["awayTeamID"], strip_tags($line[$key]["awayPartName"]), $line[$key]["home_score"], $line[$key]["away_score"], 
                $line[$key]["stages"]["2"]["0"], $line[$key]["stages"]["2"]["1"], $line[$key]["stages"]["3"]["0"], $line[$key]["stages"]["3"]["1"], $line[$key]["stages"]["4"]["0"], $line[$key]["stages"]["4"]["1"] 
            ],
            ];
            // Объект - диапазон значений
            $ValueRange = new Google_Service_Sheets_ValueRange();
            // Устанавливаем наши данные
            $ValueRange->setValues($values);
            // Указываем в опциях обрабатывать пользовательские данные
            $options = ['valueInputOption' => 'USER_ENTERED'];
            // Добавляем наши значения в последнюю строку (где в диапазоне A1:Z все ячейки пустые)
            $service->spreadsheets_values->append('1Uchevo9xfyq0-reRsiH9scTYU_7u8aUHmYA1TjyHPHo', $range, $ValueRange, $options);
        }

        


    }
}



// $requests = [
// 	new Google_Service_Sheets_Request( [
// 		'repeatCell' => [
 
// 			// Диапазон, который будет затронут
// 			"range" => [
// 				"sheetId"          => $spreadsheetId, // ID листа
// 				"startRowIndex"    => 3,
// 				"endRowIndex"      => 5,
// 				"startColumnIndex" => 3,
// 				"endColumnIndex"   => 5
// 			],
 
// 			// Формат отображения данных
// 			// https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets#CellFormat
// 			"cell"  => [
// 				"userEnteredFormat" => [
// 					// Фон (RGBA)
// 					// https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets#Color
// 					"backgroundColor"     => [
// 						"green" => 1,
// 						"red"   => 1
// 					],
// 					// // https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets#HorizontalAlign
// 					// "horizontalAlignment" => "CENTER",
// 					// // https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets#padding
// 					// "padding"             => [
// 					// 	"left"   => 10,
// 					// 	"bottom" => 50,
// 					// 	"right"  => 30,
// 					// 	"top"    => 11
// 					// ],
// 					// // https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets#textformat
// 					// "textFormat"          => [
// 					// 	"bold"      => true,
// 					// 	"fontSize"  => 25,
// 					// 	"italic"    => true,
// 					// 	"underline" => true
// 					// ]
// 				]
// 			],
 
// 			"fields" => "UserEnteredFormat(backgroundColor)"
// 		]
// 	] )
// ];
 
// $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest( [
// 	'requests' => $requests
// ] );
 
// $service->spreadsheets->batchUpdate( $spreadsheetId, $batchUpdateRequest );





// foreach ($excelData as $data => $value) {
//     echo $excelData[$data];
//     echo "<br>";
// }
//var_dump($excelData );