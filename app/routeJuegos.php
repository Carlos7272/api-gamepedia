<?php

use App\Exception\PDOInitializeException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Service\JuegoService;
use App\Service\PlataformaService;
use App\Service\GeneroService;

return function (App $app) {
     $app->group('/juegos',
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
                                    $data = $service->retrieveByFilter($params['name'], $params['idPlatform'], $params['idGender'], $params['ascending']);
                            return utilResponse($response, $data, 200);
                        }catch (Exception $ex){
                            return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }
                }
            );

            //Delete game by id
            $group->delete('/{idGame}',
                function (Request $request, Response $response) {
                    $id = (int)$request->getAttribute('idGame');
                    try {
                        $service = new JuegoService();
                        if (!$service->exist($id))
                            return utilResponse($response, ['message' => 'Juego: '.$id.' no existe'], 404);
                        $service->deleteById($id);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    return utilResponse($response, ['message' => 'Juego: '.$id.' se elimino correctamente'], 200);
                }
            );

            //Update game
            $group->put('/{idGame}',
                function (Request $request, Response $response) {
                    $id = $request->getAttribute('idGame');
                    try {
                        $service = new JuegoService();
                        $serviceGender = new GeneroService();
                        $servicePlatform = new PlataformaService();
                        if (!is_numeric($id))
                            return utilResponse($response, ['message' => 'El id no en numérico'], 404);
                        if (!$service->exist((int)$id))
                            return utilResponse($response, ['message' => 'Juego: '.$id.' no existe'], 404);
                        $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                        parametersValidationInUpdate($body, $serviceGender, $servicePlatform);
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
                        $servicePlatform = new PlataformaService();
                        $serviceGender = new GeneroService();
                        $body = $request->withParsedBody(json_decode(file_get_contents('php://input'), true))->getParsedBody();
                        parametersValidationInCreate($body, $servicePlatform, $serviceGender);
                        $service->create($body);
                    }catch (PDOInitializeException $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 500);
                    }catch (Exception $ex){
                        return utilResponse($response, ['message' => $ex ->getMessage()], 400);
                    }
                    return utilResponse($response, ['message' => 'Juego creado correctamente'], 200);
                }
            );

            function utilResponse(Response $response, $data, $status){
                $response->getBody()->write(json_encode($data, true));
                return $response -> withHeader('Content-type', 'application/json')
                                 -> withStatus($status);
            }

            function isValidQueryParamsJuegos($params): bool
            {
                $name = $params['name'];
                $idPlatform = $params['idPlatform'];
                $idGender = $params['idGender'];
                $ascending = $params['ascending'];

                if (is_null($name) && is_null($idPlatform) && is_null($idGender))
                    return false;
                if (!is_null($ascending)) {
                    $ascending = strtolower($ascending);
                    if ($ascending != "true" && $ascending != "false") return false;
                }
                if (!is_null($idPlatform) && !is_numeric($idPlatform)) return false;
                if (!is_null($idGender) && !is_numeric($idGender)) return false;
                return true;
            }

            function parametersValidationInCreate($body, $servicePlatform, $serviceGender){
                $errors = 0;
                $errorsMsg = [];

                if(empty($body['name'])){
                    $errorMsg = "El nombre es un campo requerido ";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if (empty($body['image'])) {
                    $errorMsg = "La imagen es un campo requerido";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if (empty($body['id_platform'])) {
                    $errorMsg = "La plataforma es un campo requerido";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if (!$servicePlatform->exist($body['id_platform'])) {
                    $errorMsg = "El id de plataforma no existe";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if (isset($body["description"]) && strlen($body["description"]) > 255) {
                    $errorMsg = "La descripcion es un campo de menos de 255 caracteres";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if (isset($body["url"]) && strlen($body["url"]) > 80) {
                    $errorMsg = "La url es un campo de menos de 80 caracteres";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if(empty($body['id_gender'])){
                    $errorMsg = "El id genero es un campo requerido ";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if (!empty($body["id_gender"]) && (!$serviceGender->exist($body['id_gender']))) {
                    $errorMsg = "El id de genero no existe";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if (isset($body["type_image"]) && !in_array($body['type_image'], ['gif','jpg','png'])) {
                    $errorMsg = "El tipo de archivo no es valido";
                    $errorsMsg[] = $errorMsg;
                    $errors ++;
                }
                if ($errors > 0){
                    throw new Exception(implode(". ", $errorsMsg));
                }
            }

            function parametersValidationInUpdate($body, $serviceGender, $servicePlatform){
                if (!isset($body["name"]) && !isset($body["image"]) && !isset($body["description"]) && !isset($body["type_image"]) && !isset($body["url"]) && !isset($body["idGender"]) && !isset($body["idPlatform"])) throw new Exception("Request vacío", 400);
                if (isset($body["image"]) && empty($body['image']) ) throw new Exception("El campo imagen es requerido", 400);
                if (isset($body["type_image"]) && empty($body['type_image'])) throw new Exception("El campo tipo de imagen es requerido", 400);
                if (isset($body["type_image"]) && !in_array($body['type_image'], ['gif','jpg', 'png'])) throw new Exception("El tipo de archivo no es valido", 400);;
                if ((isset($body["description"]))&&(strlen($body["description"]) > 255)) throw new Exception("La descripción es un campo menor a 255 caracteres", 400);
                if (isset($body["url"]) && strlen($body["url"]) > 80) throw new Exception("La url es un campo de menos de 80 caracteres", 400);
                if (isset($body["idGender"]) && !$serviceGender->exist($body['idGender'])) throw new Exception("El campo id genero no se encontró", 400);
                if (isset($body["idPlatform"]) && !$servicePlatform->exist($body['idPlatform'])) throw new Exception("El campo id plataforma no se encontro", 400);
            }
        }
    );
};