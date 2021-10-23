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

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
});

$app->get('/users', function ($request, $response) use ($repo) {
    $params = [
        'users' => $repo->all()
    ];
    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
});

$app->get('/users/new', function ($request, $response) {
    $params = [
        'user' => ['nickname' => '', 'email' => '', 'id' => '']
    ];
    return $this->get('renderer')->render($response, 'users/new.phtml', $params);
});

$app->post('/users', function ($request, $response) use ($repo) {
    $validator = new Validator();
    $params = $request->getParsedBody();
    $user = $request->getParsedBodyParam('user');
    if ($validator->validate($user)) {
        $repo->save($user);
        return $response->withRedirect('/users');
    } else {
        return $this->get('renderer')->render($response, "users/new.phtml", $params)->withStatus(422);
    }
});

$app->run();
