<?php
require_once('./models/exception/NotFoundException.php');
require_once('./models/user_routes/room/RoomRoutes.php');
require_once('./models/exception/AuthorizationException.php');
require_once('./models/exception/AuthenticationException.php');


class UserRoutes
{
    private Request $request_data;
    private Middleware $middleware;
    private array $url;
    private string $parent_route;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function __construct(Request $request_data, string $parent_route)
    {
        $this->request_data = $request_data;
        $this->parent_route = $parent_route;

        if ($this->request_data->is_header_provided()) { 

            $this->middleware = new Middleware($this->request_data->get_header());

            if($parent_route == 'instructor'){
                if (!$this->middleware->is_instructor()) {
                    throw new AuthenticationException();
                }
            }else if($parent_route == 'student'){
                if (!$this->middleware->is_student()) {
                    throw new AuthenticationException();
                }
            }

        } else {
            throw new AuthenticationException();
        }

        $trimmed_url = '';

        if($parent_route == 'instructor'){
            $trimmed_url= str_replace('instructor/', '', $this->request_data->get_request_url());
        }else if($parent_route == 'student'){
            $trimmed_url= str_replace('student/', '', $this->request_data->get_request_url());
        }

        $this->request_data->set_url($trimmed_url);
        $this->url = explode('/', $trimmed_url);
    }

    /**
     * @throws NotFoundException
     */
    public function handle_url(): void
    {
        $parent_route = $this->parent_route;
        $url = $this->url;
        $current_route = $url[0];
        $count = count($url);
        $next_route = null;

        if ($count > 1) {
            $next_route = $url[1];
        }

        switch ($current_route) {
            case ('room' || 'rooms'):
                $room_routes = new RoomRoutes($this->request_data, $this->middleware, $parent_route);
                $room_routes->handle_url();
                break;
            default:
                throw new NotFoundException();
        }
    }
}
