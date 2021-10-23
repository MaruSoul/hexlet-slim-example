<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';
// Контейнеры в этом курсе не рассматриваются (это тема связанная с самим ООП), но если вам интересно, то посмотрите DI Container
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Fakeldev\HexletSlimExample\Validator;
use Fakeldev\HexletSlimExample\UserRepository;


$repo = new UserRepository();

$container = new Container();
$container->set('renderer', function () {
    return new PhpRenderer(__DIR__ . '/../templates');
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) use ($router) {
    $params = [
        'router' => $router,
    ];

    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

$app->get('/users', function ($request, $response) use ($repo, $router) {
    $search = $request->getQueryParam('search');
    $params = [
        'users' => $repo->find($search),
        'search' => $search,
        'router' => $router,
    ];
    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
})->setName('users');

$app->get('/users/new', function ($request, $response) use ($router) {
    $params = [
        'user' => ['nickname' => '', 'email' => '', 'id' => ''],
        'router' => $router,
    ];
    return $this->get('renderer')->render($response, 'users/new.phtml', $params);
})->setName('users/new');

$app->post('/users', function ($request, $response) use ($repo, $router) {
    $validator = new Validator();
    $params = $request->getParsedBody();
    $user = $request->getParsedBodyParam('user');
    if ($validator->validate($user)) {
        $repo->save($user);
        return $response->withRedirect($router->urlFor('users'));
    } else {
        return $this->get('renderer')->render($response, "users/new.phtml", $params)->withStatus(422);
    }
});

$app->run();
