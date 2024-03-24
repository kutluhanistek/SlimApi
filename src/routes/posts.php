<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


global $app;

$api_url = 'https://jsonplaceholder.typicode.com/posts';
$data = file_get_contents($api_url);
$data_array = json_decode($data, true);


$app->get('/api/posts/save', function (Request $request, Response $response) use ($data_array) {
        $db = new Db();
    try {
        $db = $db->connection();
        foreach ($data_array as $item) {
            $statement = $db->prepare("INSERT INTO posts (user_id, title, body) VALUES (:userId, :title, :body)");
            $statement->execute([
                ':userId' => $item['userId'],
                ':title' => $item['title'],
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

$app->get('/api/posts', function (Request $request, Response $response) {
        $db = new Db();
    try {
        $pdo = $db->connection();
        $stmt = $pdo->query("select * from posts");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($data))
        {
            $response->getBody()->write(json_encode($data));
            return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json");
        }
        else
        {
            $errorArray = array('status' => 'False', 'message' => 'Posts not found');
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

