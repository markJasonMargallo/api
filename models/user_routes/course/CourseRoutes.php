<?php
require_once('./models/user_routes/course/CourseService.php');
require_once('./models/exception/NotFoundException.php');
require_once('./models/user_routes/activity/ActivityRoutes.php');
require_once('./models/user_routes/student/StudentRoutes.php');
require_once('./models/user_routes/room/RoomRoutes.php');


class CourseRoutes
{
    private CourseService $course_service;
    private Request $request_data;
    private Middleware $middleware;
    private array $url;
    private string $method;
    private string $parent_route;

    public function __construct(Request $request_data, Middleware $middleware, string $parent_route)
    {
        $this->request_data = $request_data;
        $this->middleware = $middleware;
        $this->parent_route = $parent_route;


        $this->url = explode('/', $this->request_data->get_request_url());

        $trimmed_url = str_replace('room/', '', $this->request_data->get_request_url());
        $this->request_data->set_url($trimmed_url);

        $this->method = $this->request_data->get_request_method();
        $this->course_service = new CourseService();
    }

    /**
     * @throws NotFoundException
     */
    public function handle_url(): void
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


        // case ('room' || 'rooms'):
            //     $room_routes = new RoomRoutes($this->request_data, $this->middleware, $parent_route);
            //     $room_routes->handle_url();
            //     break;


        if ($next_route == 'activity' || $next_route == 'activities') {

            $activity_routes = new ActivityRoutes($this->request_data, $this->middleware, $this->parent_route);
            $activity_routes->handle_url();

        } else if ($next_route == 'room' || $next_route == 'rooms') {
            $room_routes = new RoomRoutes($this->request_data, $this->middleware, $parent_route);
            $room_routes->handle_url();

        } else if ($next_route == 'student' || $next_route == 'students') {

            $student_routes = new StudentRoutes($this->request_data, $this->middleware);
            $student_routes->handle_url();

        } else {

            if ($parent_route == 'instructor') {

                switch ($this->method) {

                    case 'POST':
                        if ($current_route == 'course') {
                            echo 'here';
                            echo json_encode($this->course_service->add_course($request_body, $this->middleware->get_owner_id()));
                        }
                        break;
                    case 'GET':
                        if ($current_route == 'course') {

                            if ($next_route) {
                                echo json_encode($this->course_service->get_course($next_route));
                            } else {
                                throw new NotFoundException();
                            }
                        } else if ($current_route == 'courses') {

                            echo json_encode($this->course_service->get_instructor_courses($this->middleware->get_owner_id()));
                        }
                        break;
                    case 'PUT':
                        if ($current_route == 'course') {
                            echo json_encode($this->course_service->update_course($request_body));
                        }
                        break;
                    case 'DELETE':
                        if ($current_route == 'course') {
                            echo json_encode($this->course_service->delete_course($next_route));
                        }
                        break;
                    default:
                        throw new NotFoundException();
                }

            } else if($parent_route == 'student'){

                switch ($this->method) {

                    case 'POST':
                        if ($current_route == 'rooms') {
                            echo json_encode($this->room_service->get_student_rooms($request_body));
                        }
                        break;

                    case 'GET':

                        if ($params) {
                            echo json_encode($this->room_service->search_rooms($params['search']));
                        }

                        if ($current_route == 'room') {

                            if (intval($next_route) > 0) {
                                echo json_encode($this->room_service->get_room($next_route));
                            } else {
                                throw new NotFoundException();
                            }
                        }
                        break;
                    default:
                        throw new NotFoundException();
                }

            }

        }


    }
}
