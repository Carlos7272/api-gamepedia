<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Service\JuegoService;

$service = new JuegoService();

return function (App $app) {

    $app->group('/api-gamepedia/public/juego',
        function (RouteCollectorProxy $group) {

            //Get all games
            $group->get('/retrieve',
                function (Request $request, Response $response) {
                    $data = $GLOBALS['service'] ->retrieve();      //setear errores en caso que haya exception
                    return utilResponse($response, $data);
                }
            );

            //Get by name, plataforma, genero and with an orden
            $group->get('/retrieve/name/{name}/platform/{idPlatform}/gender/{idGender}/order-by/{order}',
                function (Request $request, Response $response,  array $args) {         //validar parametros antes de llamar a service
                    $data = $GLOBALS['service'] ->retrieveByFilter($args['name'], $args['idPlatform'], $args['idGender'], $args['order']);
                    return utilResponse($response, $data);
                }
            );

            //Delete game by id
            $group->delete('/delete/{idGame}',
                function (Request $request, Response $response, array $args) {
                    $data = $GLOBALS['service'] ->deleteById($args['idGame']);
                    //devolver en json un mensaje success
                    return utilResponse($response, $data);      //todo:mejorar la respuesta para el json
                }
            );

            //Update game
            $group->put('/update/{idGame}',
                function (Request $request, Response $response) {
                    $id = $request->getAttribute('idGame');
                    //se parsea el request para obtener el resto de los parametros del json
                    $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                    $data = $GLOBALS['service'] ->updateById((int)$id, $body);
                    //todo: devolver en json un mensaje success
                    return utilResponse($response, $data);
                }
            );

            //Create game
            $group->post('/create',
                function (Request $request, Response $response) {
                    $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                    $data = $GLOBALS['service'] ->create($body);
                    //devolver en json un mensaje success
                    return utilResponse($response, $data);
                }
            );

            function utilResponse(Response $response, $data){
                $response = $response->withHeader('Content-type', 'application/json');
                $response->getBody()->write(json_encode($data, true));
                return $response;
            }
        }
    );
};

