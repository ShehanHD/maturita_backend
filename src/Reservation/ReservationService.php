<?php


class ReservationService
{
    private ReservationRepository $reservationRepository;

    /**
     * ReservationService constructor.
     */
    public function __construct()
    {
        $this->reservationRepository = new ReservationRepository();
    }

    public function getBookingByTripId($TRIP_ID)
    {
        Authentication::verifyJWT()
            ? $this->reservationRepository->getBookingByTripId(Authentication::getId(), $TRIP_ID)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function bookATrip($TRIP_ID)
    {
        Authentication::verifyJWT()
            ? $this->reservationRepository->bookATrip(Authentication::getId(), $TRIP_ID)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function bookingState($TRIP_ID, $BODY)
    {
        Authentication::verifyJWT()
            ? $this->reservationRepository->bookingState(Authentication::getId(), $TRIP_ID, $BODY)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function getAllBookingParticipated()
    {
        Authentication::verifyJWT()
            ? $this->reservationRepository->getAllBookingParticipated(Authentication::getId())
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }
}