<?php
/**
 * Created by PhpStorm.
 * User: York
 * Date: 04.12.13
 * Time: 14:49
 */

class Gmap {

	private $center = array(35.115415, 33.434143);
	private $zoom = 9;
	private $latID = 'gLat';
	private $lngID = 'gLng';
	private $APIkey;
	private $mark = false;
	private $init = false;
	private $putMark = true;
	private $width = 800;
	private $height = 600;

	public function __construct($key = ''){
		$this->APIkey = $key;
	}

	public function SetLatLng($lat, $lng){
		$this->center = array($lat, $lng);
	}
	public function Init(){
		$this->init = true;
	}

	public function CantChangeMark(){
		$this->putMark = false;
	}

	public function Mark($lat, $lng){
		$this->mark = array($lat, $lng);
	}

	public function SetZoom($zoom){
		$this->zoom = $zoom;
	}

	public function SetSize($width, $height){
		$this->width = $width;
		$this->height = $height;
	}
	public function FieldsId($lat, $lng){
		$this->latID = $lat;
		$this->lngID = $lng;
	}

	public function GetMap(){

		if(!empty($this->center))
			return $this->_Template();
		else
			return 'Отсутствуют координаты';
	}


	private function _Template(){

		$ret = '

<div class="map" style="width: '.$this->width.'px; height: '.$this->height.'px; clear: both;">';
		if($this->init)
			$ret .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
			<script>window.onload = function(){initialize();}</script>';

		$ret .= '
		<script type="text/javascript">
		var map;
		var markersArray = [];

	function initialize() {
		var haightAshbury = new google.maps.LatLng('.$this->center[0].','.$this->center[1].');
		var mapOptions = {
			zoom: '.$this->zoom.',
			center: haightAshbury,
		};
		map =  new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

		'.($this->mark ? 'addMarker(new google.maps.LatLng('.$this->mark[0].', '.$this->mark[1].'));' : '');

		if($this->putMark){
			$ret .= '
			google.maps.event.addListener(map, "click", function(event) {
				addMarker(event.latLng);
			});
			';
		}
	$ret .= '}';

	if($this->putMark){
		$ret .= '
		function addMarker(location) {
			deleteOverlays();
			marker = new google.maps.Marker({
				//draggable:true,
				position: location,
				map: map
			});
			var loc = location.toString();
			loc = loc.replace(")", "");
			loc = loc.replace("(", "");
			loc = loc.split(", ");

			$("#'.$this->latID.'").val(loc[0]);
			$("#'.$this->lngID.'").val(loc[1]);

			markersArray.push(marker);
		}


		// Deletes all markers in the array by removing references to them
		function deleteOverlays() {
			if (markersArray) {
				for (i in markersArray) {
					markersArray[i].setMap(null);
				}
				markersArray.length = 0;
			}
		}
';
	}

	$ret .= '</script>

<div id="map_canvas" style="width: '.$this->width.'px; height: '.$this->height.'px"></div>
</div>
	';

		return $ret;
	}

} 