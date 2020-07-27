<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require '../config.php';
require 'simple_html_dom.php';

$app = AppFactory::create();
$app->setBasePath(BASEAPI);
$baseurl = BASEURL;

$app->post('/post', function (Request $request, Response $response, $args) use($baseurl) {
    $data = $request->getParsedBody();

    $html = new simple_html_dom();
    
    @$html = file_get_html($data['url']);

    if($html){
        $h1 = $html->find('#post-infos .post-titulo', 0)->content;
        $chapeu = trim($html->find('#post-infos .post-chapeu', 0)->content);
        if(empty($chapeu)){
            $chapeu = $html->find('#post-infos .post-categoria', 0)->content;
        }
        $intro = $html->find('#post-infos .post-introducao', 0)->content;
        $url = $html->find('#post-infos .post-foto-capa-l', 0)->content;
        $json = array('h1' => $h1, 'chapeu' => $chapeu, 'intro' => $intro, 'url' => $url);
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