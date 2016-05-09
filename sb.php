<?php
#"207251779638,11081890741,517762121588320,10196659501,6597757578,118428791504396,23680604925"

$token = 'EAABe5eNwsAkBAOipRO75fsERyNDmHgw5BL0GwPNpxaZBnWtLZBoGsi0LZBHeQLTEgXcqTxZBYhuTAchvoJvq6zu5itZCyVz1cu1i4nnC83iFlnxSB2VYsmeTB5VXkEoYSZC1761UFtZA4A0YsNZAiv9ME8bClOSthMAZD';

$ids = isset($argv[1]) ? $argv[1] : NULL;

if(!$ids){
	die("define ids as first param");
}

$ids = explode(",", $ids);
$fields = [
	'location.latitude',
	'location.longitude',
	'location.city',
	'location.country',
	'likes',
	'checkins',
	'name',
];
$params = [];
foreach($ids as $id){
	try{
		$sites[$id] = getSiteInfoFromFB($token, $id, $fields);
	}catch(NoGpsException $ex){
		//site does not have location -> ignore it
	}
}
$comparator = new DistanceComparator();
usort($sites, function($v1, $v2) use ($comparator){
	$a = $comparator->getDistanceFromRoot($v1);
	$b = $comparator->getDistanceFromRoot($v2);
	if($a == $b){
		return 0;
	}
	return $a > $b ? 1 : -1;

});
printSites($sites);


function printSites($sites){
	$comparator = new DistanceComparator();
	echo "název stránky;";
	echo "město a stát;";
	echo "počet fanoušků;";
	echo "počet checkinu;";
	echo "distance;";
	echo PHP_EOL;

	/** @var $site SiteInfo $site */
	foreach($sites as $site){
		echo $site->getName().";";
		echo $site->getCity().";";
		echo $site->getCountry().";";
		echo $site->getLikes().";";
		echo $site->getChekins().";";
		$comparator->getDistanceFromRoot($site);
		echo PHP_EOL;
	}
}


/**
 * @param string $token
 * @param string $id
 * @param string[] $params
 * @return mixed
 */
function getSiteInfoFromFB($token, $id, array $fields = []){
	$fbUrl = "graph.facebook.com";
	$headers = array(
		'Method: GET',
		'Connection: Keep-Alive',
		'Content-Type: text/xml; charset=utf-8',
	);

	$params['access_token'] = $token;
	if(sizeof($fields)){
		$params['fields'] = implode(",", $fields);
	}
	$handle = curl_init();

	array_walk($params, function(&$value, $key){
		$value = $key."=".$value;
	});
	$completeUrl = "https://".$fbUrl . "/{$id}"."?" . implode("&", $params);
	curl_setopt($handle, CURLOPT_URL, $completeUrl);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($handle, CURLOPT_POST, false );
	#echo$completeUrl.PHP_EOL;


	$json = json_decode(curl_exec($handle));
	if(!isset($json->location->latitude) || !isset($json->location->longitude)){
		throw new NoGpsException("FB does not return valid data");
	}

	$latitude = $json->location->latitude;
	$longitude = $json->location->longitude;

	$city = "";
	$country = "";
	$likes = "";
	$chekins = "";
	$name = "";

	if(isset($json->location->city)){
		$city = $json->location->city;
	}
	if(isset($json->location->country)){
		$country = $json->location->country;
	}
	if(isset($json->likes)){
		$likes = $json->likes;
	}
	if(isset($json->chekins)){
		$chekins = $json->chekins;
	}
	if(isset($json->name)){
		$name = $json->name;
	}

	$location = new Location($latitude, $longitude);

	return new SiteInfo(
		$location,
		$city,
		$id,
		$country,
		$likes,
		$chekins,
		$name
	);
}



class NoGpsException extends \Exception{}

class SiteInfo{

	/**
	 * @var Location
	 */
	private $location;

	/**
	 * @var string
	 */
	private $city;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @var int
	 */
	private $likes;

	/**
	 * @var int
	 */
	private $chekins;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param $location
	 * @param $city
	 * @param $id
	 * @param $country
	 * @param $likes
	 * @param $chekins
	 * @param $name
	 */
	function __construct($location, $city, $id, $country, $likes, $chekins, $name) {
		$this->location = $location;
		$this->city = $city;
		$this->id = $id;
		$this->country = $country;
		$this->likes = $likes;
		$this->chekins = $chekins;
		$this->name = $name;
	}

	/**
	 * @return Location
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * @param Location $location
	 */
	public function setLocation($location) {
		$this->location = $location;
	}

	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $city
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @param string $country
	 */
	public function setCountry($country) {
		$this->country = $country;
	}

	/**
	 * @return int
	 */
	public function getLikes() {
		return $this->likes;
	}

	/**
	 * @param int $likes
	 */
	public function setLikes($likes) {
		$this->likes = $likes;
	}

	/**
	 * @return int
	 */
	public function getChekins() {
		return $this->chekins;
	}

	/**
	 * @param int $chekins
	 */
	public function setChekins($chekins) {
		$this->chekins = $chekins;
	}




}


class DistanceComparator {

	/**
	 * @return Location
	 */
	public function getRootLocation(){
		//Prague
		return new Location(50.0595854,14.3255403);
	}


	/**
	 * calculate distance in miles from root
	 * @param SiteInfo $site
	 * @return float
	 */
	public function getDistanceFromRoot(SiteInfo $site){
		return $this->getDistanceBetween($this->getRootLocation(), $site->getLocation());
	}

	/**
	 * calculate distance in miles
	 *
	 * @param Location $from
	 * @param Location $to
	 * @return float
	 */
	public function getDistanceBetween(Location $from, Location $to){
		$theta = $from->getLongitude() - $to->getLongitude();
		$dist = sin(deg2rad($from->getLatitude())) * sin(deg2rad($to->getLatitude())) +  cos(deg2rad($from->getLatitude())) * cos(deg2rad($to->getLatitude())) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;

		return $miles;
	}
}


class Location {
	/**
	 * @var float
	 */
	private $latitude;

	/**
	 * @var float
	 */
	private $longitude;

	function __construct($latitude, $longitude) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	/**
	 * @return float
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * @return float
	 */
	public function getLongitude() {
		return $this->longitude;
	}




}
