<?php
/*Note: This is a crude file, need to optimze this file greatly!. (But it does works awesome :D) */
// Need to Fix: Need to move all repository/db codes to seperate file.
// Need to add seperate util function to avoid clustering index.php
// Need to do, routes should be seperate, no logic codes should be present here
require __DIR__.'/../vendor/autoload.php';

use Model\Blog;

Class indexLoader
{

    private $slimApp;
    private $slimRequest;
    private $slimResponse;

    function __construct() {
        $this->slimApp = new \Slim\Slim();
        $this->slimRequest = $this->slimApp->request();
        $this->slimResponse = $this->slimApp->response();
        $this->initializeRouting();
        $this->run();
    }

    function initializeRouting() {
        $app = $this->slimApp;
        $req = $this->slimRequest;
        $slimObj = $this;

        $app->get('/', function () {
            echo 'Welcome';
        });

        // CREATE BLOG //
        $app->map('/blogs', function () use ($req, $slimObj) {
            if($slimObj->isOptions()) {
               return;
            }
            $body = $req->getBody();
            $parsedBody = json_decode($body, true);
            $name = $parsedBody['name'];
            $description = $parsedBody['description'];
            $data = array();
            if (!empty($name) && !empty($description)) {
                $blog = new Blog();
                $blog->name = $name;
                $blog->description = $description;
                if ($blog->save()) {
                    $data['status'] = 'success';
                    $data['message'] = 'Blog created!';
                    return $slimObj->responseHanlder($data);
                }
            }
            $data['status'] = 'failure';
            $data['message'] = 'Blog creation failed! (I have no idea why!)';
            $slimObj->responseHanlder($data);
        })->via('OPTIONS', 'PUT');

        // UPDATE BLOG //
        $app->map('/blogs', function () use ($req, $slimObj) {
            if($slimObj->isOptions()) {
               return;
            }
            $body = $req->getBody();
            $parsedBody = json_decode($body, true);
            $id = $parsedBody['id'];
            $name = $parsedBody['name'];
            $description = $parsedBody['description'];
            $data = array();
            if (!empty($id) &&!empty($name) && !empty($description)) {
                $blogObj = new Blog();
                $blog = $blogObj::id($id);
                $blog->name = $name;
                $blog->description = $description;
                if ($blog->save()) {
                    $data['status'] = 'success';
                    $data['message'] = 'Blog updated!';
                    return $slimObj->responseHanlder($data);
                }
            }
            $data['status'] = 'failure';
            $data['message'] = 'Blog update failed! (I have no idea why!)';
            $slimObj->responseHanlder($data);
        })->via('OPTIONS', 'POST');

        // GET BLOG //
        $app->map('/blogs', function () use ($slimObj) {
            if($slimObj->isOptions()) {
               return;
            }
            $blogObj = new Blog();
            $blogs = $blogObj::all();
            $return = array();
            foreach ($blogs as $key => $blog) {
                $temp = array();
                $temp['id'] = $key;
                $temp['name'] = $blog->name;
                $temp['description'] = $blog->description;
                array_push($return, $temp);
            }
            $data['status'] = 'success';
            $data['data'] = $return;
            $slimObj->responseHanlder($data);
        })->via('OPTIONS', 'GET');

        $app->map('/blogs/:id', function ($id) use ($slimObj) {
            if($slimObj->isOptions()) {
               return;
            }
            $blogObj = new Blog();
            $blog = $blogObj::id($id);
            $blobArray = $blog->toArray();
            if(!empty($blog)) {
                $data['status'] = 'success';
                $temp['id'] = $blobArray['_id']->{'$id'};
                $temp['name'] = $blobArray['name'];
                $temp['description'] = $blobArray['description'];
                $data['data'] = $temp;
                return $slimObj->responseHanlder($data);
            }
            $data['status'] = 'failure';
            $data['message'] = 'No record found!';
            $slimObj->responseHanlder($data);
        })->via('OPTIONS', 'GET');

        $app->map('/blogs/remove/:id', function ($id) use ($slimObj) {
            if($slimObj->isOptions()) {
               return;
            }
            $blogObj = new Blog();
            $blog = $blogObj::id($id);
            if(!empty($blog)) {
                $blog->delete();
                $data['status'] = 'success';
                $data['data'] = 'Deleted succesfully';
                return $slimObj->responseHanlder($data);
            }
            $data['status'] = 'failure';
            $data['message'] = 'Deleted failed. Possibly no record found';
            $slimObj->responseHanlder($data);
        })->via('OPTIONS', 'DELETE');
    }

    function isOptions() {
        $request = $this->slimRequest;
        if($request->isOptions()) {
            $this->responseHanlder('success');
            return true;
        }
    }

    function responseHanlder($data = null) {
        $res = $this->slimResponse;
        $res->header('Content-Type', 'application/json');
        $res->header('Access-Control-Allow-Origin', '*');
        $res->header('Access-Control-Allow-Headers', 'x-requested-with, content-type');
        $res->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $res->write(json_encode($data));
    }

    function run() {
        $this->slimApp->config('debug', true);
        $this->slimApp->run();
    }

}
$loadIt = new indexLoader();