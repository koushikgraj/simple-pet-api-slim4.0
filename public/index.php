<?php
/**
 * This file is to get information of pets from SQLite DB
 *
 * This file is Slim 4.0 PHP framework
 *
 * @author     Koushik G <koushikgraj@gmail.com>
 */
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

/**
 *
 * Get list of all pets
 *
 * @param    Request  $request Request object
 * @param    Response  $response Response object
 *
 * @return   Response  $response object with JSON data
 *
 */
$app->get('/pet-list', function (Request $request, Response $response) {
	$db = dbConnection();
	$sql = sql();	
	$results = $db->query($sql);
	$arr_data = [];
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		$arr_data[] = $row;
	}
    $response->getBody()->write(json_encode($arr_data));
    return $response;
});

/**
 *
 * Get a pet details
 *
 * @param    Request  $request Request object
 * @param    Response  $response Response object
 * @param    array  $args arguments passed from the query string
 *
 * @return   Response  $response object with JSON data
 *
 */
$app->get('/pet-details/{id}', function (Request $request, Response $response, $args) {
	$db = dbConnection();
	$sql = sql();
	$sql .= " WHERE p.id=:id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':id', $args['id'], SQLITE3_INTEGER);
	$result = $stmt->execute();
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		$data = $row;
	}
	$message = isset($data)?$response->getBody()->write(json_encode($data)):"No data found";    
    return $response;
});

/**
 *
 * Get list of pets
 *
 * This function will return pet information based on 
 * sorting & filtering request from the user.
 *
 * @param    Request  $request Request object
 * @param    Response  $response Response object
 *
 * @return   Response  $response object with JSON data
 *
 */
$app->post('/pet-list', function (Request $request, Response $response): Response {
	$db = dbConnection();
	$data = json_decode($request->getBody(), true);
	$filter_fields = array(
			'name' => 'p.name',
			'age' => 'p.age',
			'personality' => 'p.personality',
			'breed' => 'p.breed',
			'city' => 'l.city',
			'state' => 'l.state',
			'zip' => 'l.zip'
		);
	$sql = sql();
	if(isset($data['filter']) && count($data['filter'])>0){
		
		$sql .= " WHERE ";
		$filters = $data['filter'];
		$whr = [];
		foreach($filters as $key=>$value){
			if(isset($filter_fields[$key])){
				$whr[] = "{$key} LIKE '%{$value}%'";
			}
		}
		if(count($whr)){
			$sql .= strtr(implode(" OR ",$whr), $filter_fields);
		}
	}
    if(isset($data['sort'])){
		$sql .= " ORDER BY ".$data['sort'];
	}
	$arr_data = [];
	$results = $db->query($sql);
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		$arr_data[] = $row;
	}
    $response->getBody()->write(json_encode($arr_data));
    return $response;
});

/**
 *
 * Common database connection for sqlite
 *
 * @return   Databse resorce
 *
 */
function dbConnection()
{
	return new SQLite3('..\db\db.sqlite');
}

/**
 *
 * Common SQL query for all end points
 *
 * @return   String $sql SQL query
 *
 */
function sql()
{
	$sql = "SELECT 
				p.id AS pet_id,
				p.name AS pet_name,
				breed,
				age,
				personality,
				p.shelter_id,
				l.id AS location_id,
				l.name AS location_name,
				address,
				city,
				state,
				phone,
				zip,
				county,
				DATE(p.created_at,'%Y-%m-%d') AS pet_created_date,
				DATE(p.updated_at,'%Y-%m-%d') AS pet_updated_date
			FROM 
				pets p 
					LEFT JOIN locations l ON (p.shelter_id = l.id)";
	return $sql;
}

$app->run();