<?php


class Rest implements HttpMapping
{
    public function __construct($REQUEST_METHOD, $PARAMS, $BODY)
    {
        switch ($REQUEST_METHOD){
            case "GET":
                $this->getMapping($PARAMS, $BODY);
                break;
            case "POST":
                $this->postMapping($PARAMS, $BODY);
                break;
            case "PUT":
                $this->putMapping($PARAMS, $BODY);
                break;
            case "PATCH":
                $this->patchMapping($PARAMS, $BODY);
                break;
            case "DELETE":
                $this->deleteMapping($PARAMS, $BODY);
                break;
        }
    }

    /**
     * @param $PARAMS
     * @param $BODY
     * @return mixed
     */
    function getMapping($PARAMS, $BODY)
    {
        // TODO: Implement getMapping() method.
    }

    /**
     * @param $PARAMS
     * @param $BODY
     * @return void
     */
    function postMapping($PARAMS, $BODY)
    {
        // TODO: Implement postMapping() method.
    }

    /**
     * @param $PARAMS
     * @param $BODY
     * @return void
     */
    function deleteMapping($PARAMS, $BODY)
    {
        // TODO: Implement deleteMapping() method.
    }

    /**
     * @param $PARAMS
     * @param $BODY
     * @return void
     */
    function patchMapping($PARAMS, $BODY)
    {
        // TODO: Implement patchMapping() method.
    }
}