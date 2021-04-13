<?php

namespace App\Core;

class Route
{

    /**
     *
     * - Mảng lưu trữ route của ứng dụng
     * - Mỗi route sẽ gôm url, method, action và params
     *
     */
    private static $routes = [];

    public function name($name)
    {

    }

    /**
     *
     * Phương thức get
     *
     * @param string $url URL cần so khớp
     * @param string|callable $action Hành động khi URL được gọi. Có thể là một callback hoặc một method trong controller
     *
     * @return void
     *
     */
    public static function get($url, $action)
    {
        // Xử lý phương thức GET
        self::request($url, 'GET', $action);

        return self::$routes;
    }

    /**
     *
     * Phương thức POST
     *
     * @param string $url URL cần so khớp
     * @param string|callable $action Hành động khi URL được gọi. Có thể là một callback hoặc một method trong controller
     *
     * @return void
     *
     */
    public static function post($url, $action)
    {
        // Xử lý phương thức POST
        self::request($url, 'POST', $action);
    }


    /**
     *
     * Xử lý phương thức
     *
     * @param string $url URL cần so khớp
     * @param string $method method của route. GET hoặc POST
     * @param string|callable $action Hành động khi URL được gọi. Có thể là một callback hoặc một method trong controller
     *
     * @return void
     *
     */
    private static function request($url, $method, $action)
    {
        // kiểm tra xem URL có chứa param không. VD: post/{id}
        if (preg_match_all('/({([a-zA-Z]+)})/', $url, $params)) {
            // thay thế param bằng (.+). VD: post/{id} -> post/(.+)
            $url = preg_replace('/({([a-zA-Z]+)})/', '(.+)', $url);
        }

        // Thay thế tất cả các kí tự / bằng ký tự \/ (regex) trong URL.
        $url = str_replace('/', '\/', $url);

        // Tạo một route mới
        $route = [
            'url' => $url,
            'method' => $method,
            'action' => $action,
            'params' => $params[2]
        ];

        // Thêm route vào router.
        array_push(self::$routes, $route);
    }


    public static function map($url, $method)
    {
        // Lặp qua các route trong ứng dụng, kiểm tra có chứa url được gọi không
        foreach (self::$routes as $route) {
            // nếu route có $method
            if ($route['method'] == $method) {

                // kiểm tra route hiện tại có phải là url đang được gọi.
                $reg = '/^' . $route['url'] . '$/';

                if (preg_match($reg, $url, $params)) {

                    array_shift($params);
                    self::call_action_route($route['action'], $params);
                    return;
                }
            }
        }

        // nếu không khớp với bất kì route nào cả.
        echo '404 - Not Found';
        return;
    }

    /**
     *
     * Hàm gọi action route
     *
     * @param string|callable $action action của route
     * @param array $params Các tham số trên url
     *
     * @return void
     *
     */
    private function call_action_route($action, $params)
    {
        // Nếu $action là một callback (một hàm).
        if (is_callable($action)) {
            call_user_func_array($action, $params);
            return;
        }

        // Nếu $action là một phương thức của controller. VD: 'HomeController@index'.
        if (is_string($action)) {
            $action = explode('@', $action);
            $controller_name = 'app\\Controllers\\' . $action[0];
            $controller = new $controller_name();
            call_user_func_array([$controller, $action[1]], $params);

            return;
        }
    }
}

?>