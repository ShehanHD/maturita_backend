<?php


class TripController extends Rest
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

    /**
     * @param $PARAMS
     * @param $BODY
     */
    function getMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case "all_available":
                $this->tripService->getAllAvailable();
                break;
            case "all_driven":
                $this->tripService->getAllDriven();
                break;
            case "all_participated":
                $this->tripService->getAllParticipated();
                break;
            case "all_filtered":
                $this->tripService->getAllFiltered($PARAMS[1], $PARAMS[2], $PARAMS[3]);
                break;
            case "get_vehicles":
                $this->tripService->getVehicleByDriver();
                break;
            case "by_id":
                $this->tripService->getTripById($PARAMS[1]);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }

    /**
     * @param $PARAMS
     * @param $BODY
     */
    function postMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case "new":
                $this->tripService->newTrip($BODY);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }

    /**
     * @param $PARAMS
     * @param $BODY
     */
    function patchMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]) {
            case "close":
                $this->tripService->closeTrip($PARAMS[1]);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }


}