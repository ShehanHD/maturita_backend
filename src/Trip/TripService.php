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
}