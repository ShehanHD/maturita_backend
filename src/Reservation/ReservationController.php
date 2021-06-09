<?php


class ReservationController extends Rest
{
    private ReservationService $reservationService;
    /**
     * ReservationController constructor.
     * @param $REQUEST_METHOD
     * @param $PARAMS
     * @param $BODY
     */
    public function __construct($REQUEST_METHOD, $PARAMS, $BODY)
    {
        $this->reservationService = new ReservationService();
        parent::__construct($REQUEST_METHOD, $PARAMS, $BODY);
    }

    /**
     * @param $PARAMS
     * @param $BODY
     */
    function getMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case "all_passengers_by_trip_id":
                $this->reservationService->getBookingAllPassengersByTripId($PARAMS[1], $PARAMS[2] ?? 0);
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
            case "on":
                $this->reservationService->bookATrip($PARAMS[1]);
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
        switch ($PARAMS[0]){
            case "state":
                $this->reservationService->bookingState($PARAMS[1], $BODY);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }


}