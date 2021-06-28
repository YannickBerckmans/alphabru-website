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
		$file = 'homepage.html';
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
			//$body->write(sendMail($data, $this));
      try{
        if(($mail = sendMail($data, $this)) !== true){
  				$body->write(json_encode(['success' => false, "message" =>$mail->ErrorInfo]));
  			} else {
  				$body->write(json_encode(['success' => true, "message" => ""]));
  			}
      } catch (Exception $e){
          $body->write(json_encode(['success' => false, "message" => $e->getMessage()]));
      }

		}
		return $response->withStatus(200)->withHeader('Content-Type',"application/json;charset='utf-8");

	});

	// Redirect fallback
	$app->redirect('[{path:.*}]' ,'/',302);

};

function sendMail($data, $self){
	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPDebug = 2;
	$mail->Host       = $self->get('settings')['smtp_host'];
	$mail->SMTPAuth   = true;
	$mail->Username   = $self->get('settings')['smtp_username'];
	$mail->Password   = $self->get('settings')['smtp_password'];
	$mail->Port       = $self->get('settings')['smtp_port'];
	$mail->setFrom($self->get('settings')['smtp_from'], '');
	$mail->addAddress($self->get('settings')['smtp_to'],'');
	$mail->isHTML(true);
	$mail->Subject = "Website Alphrabru.be - ".$data['subject'];
	$mail->Body    = $data['message'] .
		"<br/><br/> From: ".$data['email'].
		"<br/>Name: ".$data['last-name'].
		"<br/>Company:" .$data['company'].
		"<br/>Phone: ".$data['phone'];

	if(!$mail->send()) {
		return $mail;
	}

	return true;
}
