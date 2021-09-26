<?php
require_once 'jwt/JWT.php';
require_once 'src/authModel.php';
require_once 'src/response.php';
use Firebase\JWT\JWT;

class Authentication extends AuthModel
{
	private $table = 'personas';
	private $key = 'clave_secreta';

	public function signIn($user)
	{
		if(!isset($user['username']) || !isset($user['password']) || empty($user['username']) || empty($user['password'])){
			$response = array(
				'result' => 'error',
				'details' => 'Los campos password y username son obligatorios'
			);
			
			Response::result(400, $response);
			exit;
		}

		$result = parent::login($user['username'], hash('sha256' , $user['password']));

		if(sizeof($result) == 0){
			$response = array(
				'result' => 'error',
				'details' => 'El usuario y/o la contraseña son incorrectas'
			);

			Response::result(403, $response);
			exit;
		}

		$dataToken = array(
			'iat' => time(),
			'data' => array(
				'id' => $result[0]['id'],
				'nombres' => $result[0]['nombres']
			)
		);

		$jwt = JWT::encode($dataToken, $this->key);

		parent::update($result[0]['id'], $jwt);

		return $jwt;
	}

	public function verify()
    {
        if(!isset($_SERVER['HTTP_API_KEY'])){
    
            $response = array(
                'result' => 'error',
                'details' => 'Usted no tiene los permisos para esta solicitud'
            );
        
            Response::result(403, $response);
            exit;
        }

        $jwt = $_SERVER['HTTP_API_KEY'];

        try {
            $data = JWT::decode($jwt, $this->key, array('HS256'));

			$user = parent::getById($data->data->id);

			if($user[0]['token'] != $jwt){
				throw new Exception();
			}
			
            return $data;
        } catch (\Throwable $th) {
            
            $response = array(
                'result' => 'error',
                'details' => 'No tiene los permisos para esta solicitud'
            );
        
            Response::result(403, $response);
            exit;
        }
    }
}
