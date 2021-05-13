<?php


class TripController extends Rest implements HttpMapping
{
    private TripService $tripService;

    /**
     * TripController constructor.
     * @param $REQUEST_METHOD
     * @param $PARAMS
     * @param $BODY
     */
    public function __construct($REQUEST_METHOD, $PARAMS, $BODY)
    {
        $this->tripService = new TripService();
        parent::__construct($REQUEST_METHOD, $PARAMS, $BODY);
    }

    function getMapping($PARAMS, $BODY)
    {
        // TODO: Implement getMapping() method.
    }

    function postMapping($PARAMS, $BODY)
    {
        // TODO: Implement postMapping() method.
    }

    function deleteMapping($PARAMS, $BODY)
    {
        // TODO: Implement deleteMapping() method.
    }

    function patchMapping($PARAMS, $BODY)
    {
        // TODO: Implement patchMapping() method.
    }
}