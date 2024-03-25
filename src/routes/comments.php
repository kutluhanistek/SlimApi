<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


global $app;

$api_url = 'https://jsonplaceholder.typicode.com/comments';
$data = file_get_contents($api_url);
$data_array = json_decode($data, true);


$app->get('/api/comments/save', function (Request $request, Response $response) use ( $data_array) {

    $db = new Db();
    try {
        $pdo = $db->connection();
        foreach ($data_array as $item) {
            $statement = $pdo->prepare("INSERT INTO comments (post_id, name, email, body) VALUES (:postId, :name, :email, :body)");
            $statement->execute([
                ':postId' => $item['postId'],
                ':name' => $item['name'],
                ':email' => $item['email'],
                ':body' => $item['body'],
            ]);

        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(["message" => "Data saved successfully"]));
        return $response;
    }catch (PDOException $e){
        $errorArray = array('message' => $e->getMessage());
        $response->getBody()->write(json_encode($errorArray));
        return $response->withStatus(404)
            ->withHeader("Content-Type", "application/json");
    }


});


$app->get('/api/posts/{post_id}/comments', function (Request $request, Response $response) {

    $db = new Db();
    $id = $request->getAttribute('post_id');
    try {
        $db = $db->connection();
        $stmt = $db->query("select * from comments where post_id = $id");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($data))
        {
            $response->getBody()->write(json_encode($data));
            return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json");
        }
        else{
            $errorArray = array('message' => "No comments found on this post");
            $response->getBody()->write(json_encode($errorArray));
            return $response->withStatus(404)
                ->withHeader("Content-Type", "application/json");
        }
    }catch (PDOException $e){
        $errorArray = array('message' => $e->getMessage());
        $response->getBody()->write(json_encode($errorArray));
        return $response->withStatus(404)
            ->withHeader("Content-Type", "application/json");
    }

    $db= null;

});


$app->get('/api/comments', function (Request $request, Response $response) {

    $db = new Db();
    try {
        $db = $db->connection();
        $stmt = $db->query("select * from comments");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($data))
        {
            $response->getBody()->write(json_encode($data));
            return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json");
        }else {
            $errorArray = array('message' => "Comments not found");
            $response->getBody()->write(json_encode($errorArray));
            return $response->withStatus(404)
                ->withHeader("Content-Type", "application/json");
        }

    }catch (PDOException $e){
        $errorArray = array('message' => $e->getMessage());
        $response->getBody()->write(json_encode($errorArray));
        return $response->withStatus(404)
            ->withHeader("Content-Type", "application/json");
    }

});



