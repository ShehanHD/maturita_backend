<?php


class Rest
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
}