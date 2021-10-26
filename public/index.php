<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';
// Контейнеры в этом курсе не рассматриваются (это тема связанная с самим ООП), но если вам интересно, то посмотрите DI Container
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Slim\Flash\Messages;
use DI\Container;
use Fakeldev\HexletSlimExample\Validator;
use Fakeldev\HexletSlimExample\UserRepository;
use Fakeldev\HexletSlimExample\Flatten;

session_start();
$repo = new UserRepository();
$flatten = new Flatten();

$container = new Container();
$container->set('renderer', function () {
    return new PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new Messages();
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

$app->get('/users', function ($request, $response, $args) use ($repo, $router) {
    $search = $request->getQueryParam('search');
    $messages = $this->get('flash')->getMessages();
    $params = [
        'users' => $repo->find($search),
        'search' => $search,
        'router' => $router,
        'flash' => $messages,
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
    $params = [
        'term' => $request->getParsedBody(),
        'router' => $router,
    ];

    $user = $request->getParsedBodyParam('user');
    if ($validator->validate($user)) {
        $repo->save($user);
        $this->get('flash')->addMessage('success', 'Успешно!');
        return $response->withRedirect($router->urlFor('users'));
    } else {
        return $this->get('renderer')->render($response, "users/new.phtml", $params)->withStatus(422);
    }
});

$app->get('/users/{id}', function ($request, $response, array $args) use ($repo, $flatten) {
    $id = $args['id'];
    $userArray = $flatten->flatten($repo->find($id));
    if (empty($userArray)) {
        return $response->withStatus(404);
    } else {
        $strUser = implode(', ', $userArray);
        return $response->write("User: {$strUser}");
    }
});

$app->run();
