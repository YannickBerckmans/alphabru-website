<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

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
	
	$app->get('/mySuperCall', function(Request $request, Response $response){
		$response->getBody()->write('POST');
		return $response;
	});
	
	// Redirect fallback
	$app->redirect('[{path:.*}]' ,'/',302);
 
};
