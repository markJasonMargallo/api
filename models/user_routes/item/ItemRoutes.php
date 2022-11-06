<?php
require_once('./models/user_routes/item/ItemService.php');
require_once('./models/exception/NotFoundException.php');
require_once('./models/user_routes/submission/SubmissionRoutes.php');

class ItemRoutes
{
    private ItemService $item_service;
    private Request $request_data;
    private Middleware $middleware;
    private $url;
    private $method;
    private $parent_route;

    public function __construct(Request $request_data, Middleware $middleware, $parent_route)
    {
        $this->request_data = $request_data;
        $this->middleware = $middleware;
        $this->parent_route = $parent_route;

        $this->url = explode('/', $this->request_data->get_request_url());

        $trimmed_url = str_replace('item/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
        $this->item_service = new ItemService();
    }

    public function handle_url()
    {
        $parent_route = $this->parent_route;
        $request_body = json_decode(file_get_contents('php://input'));
        $url = $this->url;
        $count = count($url);
        $current_route = $url[0];
        $next_route = null;
        
        if ($count > 1) {
            $next_route = $url[1];
        }

        if($next_route == 'submission' || $next_route == 'submissions'){

            $submission_routes = new SubmissionRoutes($this->request_data, $this->middleware, $this->parent_route);
            $submission_routes->handle_url();

        }else{

            if ($parent_route == 'instructor') {

                switch ($this->method) {
                    case 'POST':
                        if ($current_route == 'item' && $count == 1) {
                            echo json_encode($this->item_service->add_item($request_body));
                        }
                        break;
                    case 'GET':
                        if ($current_route == 'item' && $count == 2) {
                            if (intval($next_route) > 0) {
                                echo json_encode($this->item_service->get_item($next_route));
                            } else {
                                throw new NotFoundException();
                            }
                        } else if ($current_route == 'items' && $count == 2) {
                            echo json_encode($this->item_service->get_items($next_route));
                        }
                        break;
                    case 'PUT':
                        if ($current_route == 'item' && $count == 1) {
                            echo json_encode($this->item_service->update_item($request_body));
                        }
                        break;
                    case 'DELETE':
                        if ($current_route == 'item' && $count == 2) {
                            echo json_encode($this->item_service->delete_item($next_route));
                        }
                        break;
                    default:
                        throw new NotFoundException();
                }

            } else if ($parent_route == 'student') {

                switch ($this->method) {

                    case 'GET':
                        if ($current_route == 'item' && $count == 2) {
                            if (intval($next_route) > 0) {
                                echo json_encode($this->item_service->get_item($next_route));
                            } else {
                                throw new NotFoundException();
                            }
                        } else if ($current_route == 'items' && $count == 2) {
                            echo json_encode($this->item_service->get_items($next_route));
                        }
                        break;
                    default:
                        throw new NotFoundException();
                }
            }


            
        }

        
    }
}
