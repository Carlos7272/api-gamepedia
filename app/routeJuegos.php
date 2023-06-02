<?php

use App\Exception\PDOInitializeException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Service\JuegoService;

return function (App $app) {

    //forma ok
    //$app->group('/juegos',
     $app->group('/api-gamepedia/public/juegos',
        function (RouteCollectorProxy $group) {

            //Get all games and get all with filter
            $group->get('',
                    function (Request $request, Response $response) {
                        try {
                            $service = new JuegoService();
                            $params = $request->getQueryParams();
                            if (empty($params))
                                $data = $service->retrieve();
                            else
                                if (!isValidQueryParamsJuegos($params))
                                    return utilResponse($response, ['message' => 'error en los parametros'], 400);
                                else
                                    $data = $service->retrieveByFilter($params['name'], $params['idPlatform'], $params['idGender'], $params['order']);
                            return utilResponse($response, $data, 200);
                        }catch (Exception $ex){
                            return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }
                }
            );

            //Delete game by id
            //idGame -----no existe-----404
            //error si el id se esta utilizando...400
            $group->delete('/{idGame}',
                function (Request $request, Response $response) {
                    $id = $request->getAttribute('idGame');
                    try {
                        $service = new JuegoService();
                        $service->deleteById((int)$id);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    //return utilResponse($response, ['message' => 'Juego: '.$id.' actualizado correctamente'], 200);
                    return utilResponse($response, ['message' => 'Juego: '.$id.' se elimino correctamente'], 200);
                }
            );

            //Update game
            $group->put('/{idGame}',
                function (Request $request, Response $response) {
                    $id = $request->getAttribute('idGame');
                    try {
                        $service = new JuegoService();
                        $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                        $service->updateById((int)$id, $body);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    return utilResponse($response, ['message' => 'Juego: '.$id.' actualizado correctamente'], 200);
                }
            );

            //Create game
            $group->post('',
                function (Request $request, Response $response) {
                    try{
                        $service = new JuegoService();
                        $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                        $data = $service->create($body);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    return utilResponse($response, $data, 200);
                }
            );

            function utilResponse(Response $response, $data, $status){
                $response->getBody()->write(json_encode($data, true));
                return $response -> withHeader('Content-type', 'application/json')
                                 -> withStatus($status);
            }

            function isValidQueryParamsJuegos($params): bool
            {
                if (is_null($params['name']) || is_null($params['idPlatform']) || is_null($params['idGender']) || is_null($params['order']));
                    return false;
            }
        }
    );
};