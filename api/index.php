<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require '../config.php';
require 'simple_html_dom.php';

$app = AppFactory::create();
$app->setBasePath(BASEAPI);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->post('/post', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    $html = new simple_html_dom();
    
    @$html = file_get_html($data['url']);

    if($html){
        $titulo = $html->find('#post-infos .post-titulo', 0)->content;
        $chapeu = trim($html->find('#post-infos .post-chapeu', 0)->content);
        if(empty($chapeu)){
            $chapeu = $html->find('#post-infos .post-categoria', 0)->content;
        }
        $intro = $html->find('#post-infos .post-introducao', 0)->content;
        $foto = $html->find('#post-infos .post-foto-capa-l', 0)->content;
        $json = array('titulo' => $titulo, 'chapeu' => $chapeu, 'intro' => $intro, 'foto' => $foto);
        $response->getBody()->write(json_encode($json));

        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }else{
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
});

$app->run();