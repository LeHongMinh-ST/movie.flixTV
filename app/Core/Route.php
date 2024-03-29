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

    /**
     *
     * - Chuỗi middleware của ứng dụng
     * - Mỗi route sẽ chưa middleware để kiểm tra quyền
     *
     */
    private static $middleware = null;

    /**
     *
     * - Chuỗi prefix của ứng dụng
     * - Mỗi route sẽ gôm url, method, action và params
     *
     */
    private static $prefix = '';

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
    public static function get($url, $action, $name = '')
    {
        // Xử lý phương thức GET
        self::request($url, 'GET', $action, $name);

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
    public static function post($url, $action, $name = '')
    {
        // Xử lý phương thức POST
        self::request($url, 'POST', $action, $name);
    }


    /**
     *
     * Xử lý middleware
     *
     * @param string $middleware tên của middleware
     * @param string|callable $action Hành động khi URL được gọi.
     *
     * @return void
     *
     */
    public static function middleware($middleware, $callback)
    {
        static::$middleware = $middleware;
        call_user_func($callback);
        static::$middleware = null;
    }

    /**
     *
     * Xử lý middleware
     *
     * @param string $prefix tên của prefix
     * @param string|callable $action Hành động khi URL được gọi.
     *
     * @return void
     *
     */
    public static function prefix($prefix, $callback)
    {
        static::$prefix = $prefix . '\\';
        call_user_func($callback);
        static::$prefix = '';
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
    private static function request($url, $method, $action, $name)
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
            'name' => $name == '' ? $url : $name,
            'method' => $method,
            'action' => is_callable($action) ? $action : static::$prefix . $action,
            'params' => $params[2],
            'middleware' => static::$middleware
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

                    Middleware::check($route['middleware']);

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
            $controller_name = 'App\\Controllers\\' . $action[0];
            $controller = new $controller_name();
            call_user_func_array([$controller, $action[1]], $params);

            return;
        }
    }

    public static function getRoute()
    {
        return self::$routes;
    }
}

?>