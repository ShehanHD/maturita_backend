<?php


class TripService
{

    /**
     * TripService constructor.
     */
    private TripRepository $tripRepository;

    public function __construct()
    {
        $this->tripRepository = new TripRepository();
    }

    public function getAllAvailable()
    {
        $this->tripRepository->getAllAvailable();
    }

    public function getAllDriven()
    {
        Authentication::verifyJWT()
            ? $this->tripRepository->getAllDriven(Authentication::getId())
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function getAllParticipated()
    {
        Authentication::verifyJWT()
            ? $this->tripRepository->getAllParticipated(Authentication::getId())
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function getAllFiltered($departure, $destination, $when)
    {
        $this->tripRepository->getAllFiltered($departure, $destination, $when);
    }

    public function getTripById($ID)
    {
        $this->tripRepository->getTripById($ID);
    }

    public function getVehicleByDriver()
    {
        Authentication::verifyJWT()
            ? $this->tripRepository->getVehicleByDriver(Authentication::getId())
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function newTrip($BODY)
    {
        Authentication::verifyJWT()
            ? $this->tripRepository->newTrip(Authentication::getId(), $BODY)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function closeTrip($ID)
    {
        Authentication::verifyJWT()
            ? $this->tripRepository->closeTrip($ID)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }
}