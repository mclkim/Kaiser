<?php
/**
 * Created by PhpStorm.
 * User: 김준수
 * Date: 2018-07-15
 * Time: 오후 9:49
 */

namespace Mcl\Kaiser;

use Aura\Web\ResponseSender;
use Aura\Web\WebFactory;

class Response //extends Singleton
{
    protected $response;
    protected $response_sender;
    protected $original;

    function __construct()
    {
        $globals = array();
        $factory = new WebFactory ($globals);
        $this->response = $factory->newResponse();
        $this->response_sender = new ResponseSender ($this->response);
    }

    function setContent($content)
    {
        $this->original = $content;

        // If the content is "JSONable" we will set the appropriate header and convert
        // the content to JSON. This is useful when returning something like models
        // from routes that will be automatically transformed to their JSON form.
        if ($this->shouldBeJson($content)) {
            $this->response->headers->set('Content-Type', 'application/json');
            $content = $this->morphToJson($content);
        }

        // If this content implements the "Renderable" interface then we will call the
        // render method on the object so we will avoid any "__toString" exceptions
        // that might be thrown and have their errors obscured by PHP's handling.
        elseif ($content instanceof Renderable) {
            $content = $content->render();
        }

        $this->response->content->set($content);
        return $this;
    }

    protected function shouldBeJson($content)
    {
        return $content instanceof Jsonable || $content instanceof ArrayObject || is_array($content);
    }

    protected function morphToJson($content)
    {
        if ($content instanceof Jsonable)
            return $content->toJson();

        return json_encode($content);
    }

    function redirect($location, $code = 302, $phrase = null)
    {
        $this->response->redirect->to($location, $code, $phrase);
        return $this;
    }

    function status($code, $phrase = null, $version = null)
    {
        $this->response->status->set($code, $phrase, $version);
        return $this;
    }

    function response_sender()
    {
        return $this->response_sender->__invoke();
    }
}