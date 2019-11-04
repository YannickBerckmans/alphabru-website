<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

return function (App $app) {
    
	$app->get('/', function (Request $request, Response $response) {
		$file = 'index.html';
		if (file_exists($file)) {
			$response->getBody()->write(file_get_contents($file));
			return $response;
		} else {
			throw new \Slim\Exception\NotFoundException($request, $response);
		}
		 
		/*$response->getBody()->write('Hello world!');
        return $response;*/
    });
	
	$app->post('/form', function(Request $request, Response $response){
		$body = $response->getBody();
		$body->rewind();
		$data = $request->getParsedBody();
		if(empty($data['company']) 
			|| empty($data['last-name'])
			|| empty($data['email'])
			|| empty($data['phone'])
			|| empty($data['subject'])
			|| empty($data['message'])
		){
			$body->write(json_encode(['success' => false, "message" => "missing parameter"]));	
		} else {

			if(!sendMail($data, $this)){
				$body->write(json_encode(['success' => false, "message" => "Couldn't send email"]));	
			} else {
				$body->write(json_encode(['success' => true, "message" => ""]));
			}	
		}
		return $response->withStatus(200)->withHeader('Content-Type',"application/json;charset='utf-8");
		
	});
	
	// Redirect fallback
	$app->redirect('[{path:.*}]' ,'/',302);
 
};

function sendMail($data, $self){
	$mail = new PHPMailer();
	try{
		$mail->isSMTP();                                            
	    $mail->Host       = $self->get('settings')['smtp_host'];                   
	    $mail->SMTPAuth   = true;                                   
	    $mail->Username   = $self->get('settings')['smtp_username'];                     
	    $mail->Password   = $self->get('settings')['smtp_password'];                               
	    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     
	    $mail->Port       = 587;           
		$mail->setFrom($self->get('settings')['smtp_from'], '');
		$mail->addAddress($self->get('settings')['smtp_to'],'');
	    $mail->isHTML(true);   
		$mail->Subject = $data['subject'];
		$mail->Body    = $data['message'] . 
			"<br/><br/> From: ".$data['email'].
			"<br/>Name: ".$data['last-name'].
			"<br/>Company:" .$data['company'].
			"<br/>Phone: ".$data['phone'];

		$mail->send();	

		return true;
	} catch(Exception $e){
		return false;
	}
	

}
