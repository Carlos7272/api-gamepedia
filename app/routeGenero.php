<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Service\GeneroService;

$service = new GeneroService();

return function (App $app) {

    $app->group('/api-gamepedia/public/genero',
        function (RouteCollectorProxy $group) {

            //Get all genders
            $group->get('/retrieve',
                function (Request $request, Response $response) {
                    $data = $GLOBALS['service'] ->retrieve();      //setear errores en caso que haya exception
                    return utilResponse($response, $data);
                }
            );

            // se debería hacer para conseguir algunos géneros por nombre?

            //Delete gender by id
            $group->delete('/delete/{idGender}',
                function (Request $request, Response $response, array $args) {
                    $data = $GLOBALS['service'] ->deleteById($args['idGender']);
                    //devolver en json un mensaje success
                    return utilResponse($response, $data);      //todo:mejorar la respuesta para el json
                }
            );

            // llegado este punto, me pregunto si es necesario actualizar los géneros. Si eso, y si debe poseer
            // algún tipo de descripción, entonces se puede modificar su clase y se deja este método.
            //Update gender
            $group->put('/update/{idGender}',
                function (Request $request, Response $response) {
                    $id = $request->getAttribute('idGender');
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