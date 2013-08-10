<?php
require __DIR__.'/../vendor/autoload.php';

use Model\Blog;

$app = new \Slim\Slim();
$req = $app->request();
$res = $app->response();

$app->post('/blog/create', function () use ($req, $res) {
    $blog = new Blog();
    $blog->name = $req->post('name');
    $blog->description = $req->post('description');
    $blog->save();
    $res['Content-Type'] = 'application/json';
    $res['Access-Control-Allow-Origin'] => 'http://192.168.0.2:3501/';
    $res->write('success');
});

$app->get('/blogs/list', function () use ($res) {
    $blogObj = new Blog();
    $blogs = $blogObj::all();
    $res['Content-Type'] = 'application/json';
    $return = array();
    foreach ($blogs as $key => $blog) {
    	$temp = array();
    	$temp['id'] = $key;
    	$temp['name'] = $blog->name;
    	$temp['description'] = $blog->description;
    	array_push($return, $temp);
    }
    $res['Content-Type'] = 'application/json';
    $res->header('Access-Control-Allow-Origin', '*');
    $res->write(json_encode($return));
});

$app->config('debug', true);

$app->run();
