<?php
session_start();
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate app
$app = AppFactory::create();

$authMw = function (Request $request, RequestHandler $handler) {
    $routeContext = RouteContext::fromRequest($request);
    $route = $routeContext->getRoute();
    $routeArg = $route->getArgument('name');
    if ($_SESSION['user'] === $routeArg) {
        return $handler->handle($request);
    }
    else {
        $response = new Response();
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    } 
};

// Add route callbacks
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('Log in to access your profile');
    return $response;
})->setName('home');

$app->get('/login/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $_SESSION['user'] = $name;
    return $response
        ->withHeader('Location', '/profile/' . $name)
        ->withStatus(302);
});

$app->get('/profile/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
})->add($authMw);

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Run application
$app->run();
