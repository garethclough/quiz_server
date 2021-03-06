<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use Lib\Database;    

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require '../include.php';
    $settings = include('../settings/settings.php');
    $app = AppFactory::create();
 
    $app->addRoutingMiddleware();

    $errorMiddleware = $app->addErrorMiddleware(true, true, true);    



    Database::init($settings);

    // Define app routes
    $app->get('/hello/{name}', function (Request $request, Response $response, $args) {
        $name = $args['name'];
        $response->getBody()->write("Hello, $name");
        return $response;
    });

    $app->get('/get', function (Request $request, Response $response, $args) {
        $db = Database::get();
        $s = $db->query('SELECT * FROM question');
        $questions = [];
        while($row = $s->fetchObject()) {
            $row->options = [];
            $sOption = $db->query('SELECT * FROM question_option WHERE question_id = '.$row->question_id);
            while($optionRow = $sOption->fetchObject()) {
                $row->options[] = $optionRow;
            }

            $questions[] = $row;
        }
        $response->getBody()->write(json_encode($questions));
        return $response->withHeader('Content-Type', 'application/json');        
    });

    $app->get('/getFlags', function (Request $request, Response $response, $args) {
        $db = Database::get();
        $s = $db->query('SELECT * FROM quiz WHERE quiz_id = 1');
        $quiz = $s->fetchObject();
        $s = $db->query('SELECT * FROM flag WHERE country_name IS NOT NULL');
        $flags = [];
        while($row = $s->fetchObject()) {
            $flags[] = $row;
        }
        $json = new \StdClass();
        $json->quiz = $quiz;
        $json->flags = $flags;
        $response->getBody()->write(json_encode($json));
        return $response->withHeader('Content-Type', 'application/json');        
    });

    $app->get('/flag/{id}.svg', function (Request $request, Response $response, $args) {
        $id = (int)$args['id'];
        $file = DIR_FLAGS.DIRECTORY_SEPARATOR.$id.'.svg';
        if (file_exists($file)) {
            $svg = file_get_contents($file);
            $response->getBody()->write($svg);
        }
        return $response->withHeader('Content-Type','image/svg+xml');
    });    

    // Run app
    $app->run();    
