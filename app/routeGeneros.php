<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Service\GeneroService;
use App\Exception\PDOInitializeException;

return function (App $app) {
    $app->group('/generos',
        function (RouteCollectorProxy $group) {

            //Get all genders
            $group->get('',
                function (Request $request, Response $response) {
                    try {
                        $service = new GeneroService();
                        $data = $service->retrieve();
                        return utilResponse($response, $data, 200);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }
                }
            );

            //Delete gender by id
            $group->delete('/{idGender}',
                function (Request $request, Response $response) {
                    $id = (int)$request->getAttribute('idGender');
                    try {
                        $service = new GeneroService();
                        if (!$service->exist($id))
                            return utilResponse($response, ['message' => 'Genero: '.$id.' no existe'], 404);
                        $service->deleteById($id);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    return utilResponse($response, ['message' => 'Genero: '.$id.' se elimino correctamente'], 200);
                }
            );

            //Update gender
            $group->put('/{idGender}',
                function (Request $request, Response $response) {
                    $id = (int)$request->getAttribute('idGender');
                    try {
                        $service = new GeneroService();
                        if (!$service->exist($id))
                            return utilResponse($response, ['message' => 'Genero: '.$id.' no existe'], 404);
                        $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                        $service->updateById($id, $body);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    return utilResponse($response, ['message' => 'Genero: '.$id.' actualizado correctamente'], 200);
                }
            );

            //Create gender
            $group->post('',
                function (Request $request, Response $response) {
                    try{
                        $service = new GeneroService();
                        $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                        $service->create($body);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    return utilResponse($response, ['message' => 'Genero creado correctamente'], 200);
                }
            );
        }
    );
};