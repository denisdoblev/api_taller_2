<?php
class Response
{
	public function result($code, $response){

		header('Content-type: application/json');
		http_response_code($code);

		echo json_encode($response);
	}
}

?>