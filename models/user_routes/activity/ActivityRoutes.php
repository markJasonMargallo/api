<?php
require_once('./models/user_routes/activity/ActivityService.php');
require_once('./models/exception/NotFoundException.php');
require_once('./models/user_routes/item/ItemRoutes.php');

class ActivityRoutes
{
    private ActivityService $activity_service;
    private Request $request_data;
    private Middleware $middleware;
    private $url;
    private $method;
    private string $parent_route;

    public function __construct(Request $request_data, Middleware $middleware, $parent_route)
    {
        $this->request_data = $request_data;
        $this->middleware = $middleware;
        $this->parent_route = $parent_route;

        $this->url = explode('/', $this->request_data->get_request_url());

        $trimmed_url = str_replace('activity/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
        $this->activity_service = new ActivityService();
    }

    public function handle_url()
    {
        $parent_route = $this->parent_route;
        $request_body = json_decode(file_get_contents('php://input'));
        $params = $this->request_data->get_request_params();
        $url = $this->url;
        $count = count($url);
        $current_route = $url[0];
        $next_route = null;

        if ($count > 1) {
            $next_route = $url[1];
        }

        if ($next_route == 'item' || $next_route == 'items') {
            $item_routes = new ItemRoutes($this->request_data, $this->middleware, $this->parent_route);
            $item_routes->handle_url();
        } else {
            if ($parent_route == 'instructor') {

                switch ($this->method) {
                    case 'POST':
                        if ($current_route == 'activity' && $count == 1) {
                            echo json_encode($this->activity_service->create_activity($request_body));
                        } else if ($current_route == 'activity' && $count == 2 && $next_route == 'summary') {
                            echo json_encode($this->activity_service->get_activity_summary($request_body));
                        }
                        break;
                    case 'GET':
                        if ($params) {
                            echo json_encode($this->activity_service->search_activity($params['search']));
                        }

                        if ($current_route == 'activity' && $count == 2) {
                            if (intval($next_route) > 0) {
                                echo json_encode($this->activity_service->get_activity($next_route));
                            } else {
                                throw new NotFoundException();
                            }
                        } else if ($current_route == 'activities' && $count == 2) {
                            if (intval($next_route) > 0) {
                                echo json_encode($this->activity_service->get_activities($next_route, $this->middleware->get_owner_id()));
                            } else {
                                throw new NotFoundException();
                            }
                            // echo json_encode($this->activity_service->get_activities_by_instructor($this->middleware->get_owner_id()));
                        } else if ($current_route == 'activities') {
                            echo json_encode($this->activity_service->get_activities_by_instructor($this->middleware->get_owner_id()));
                        } else {
                            throw new NotFoundException();
                        }
                        break;
                    case 'PUT':
                        if ($current_route == 'activity') {
                            echo json_encode($this->activity_service->update_activity($request_body));
                        }
                        break;
                    case 'DELETE':
                        if ($current_route == 'activity') {
                            echo json_encode($this->activity_service->delete_activity($next_route));
                        }
                        break;
                    default:
                        throw new NotFoundException();
                }
            } else if ($parent_route == 'student') {
                switch ($this->method) {
                    case 'POST':
                        // echo $this->middleware->get_owner_id();
                        if ($current_route == 'activities' && $count == 1) {
                            echo json_encode($this->activity_service->get_activities_by_student($request_body,));
                        }
                        break;
                    case 'GET':
                        if ($params) {
                            echo json_encode($this->activity_service->search_activity($params['search']));
                        } else if ($current_route == 'activity' && $count == 2 && $next_route == 'summary') {

                            echo json_encode($this->activity_service->get_activity_summary($request_body));
                            
                        }else if ($current_route == 'activity' && $count == 2) {
                            if (intval($next_route) > 0) {
                                echo json_encode($this->activity_service->get_activity($next_route));
                            } else {
                                throw new NotFoundException();
                            }
                        } else if ($current_route == 'activities' && $count == 2) {
                            echo json_encode($this->activity_service->get_activities_by_room($next_route));
                        }
                        break;
                    default:
                        throw new NotFoundException();
                }
            }
        }
    }
}
