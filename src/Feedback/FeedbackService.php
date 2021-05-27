<?php


class FeedbackService
{
    private FeedbackRepository $feedbackRepository;

    /**
     * FeedbackService constructor.
     */
    public function __construct()
    {
        $this->feedbackRepository = new FeedbackRepository();
    }

    public function getAllFeedbacks()
    {
        Authentication::verifyJWT()
            ? $this->feedbackRepository->getAllFeedbacks(Authentication::getId())
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function addFeedback($TRIP_ID, $BODY)
    {
        Authentication::verifyJWT()
            ? $this->feedbackRepository->addFeedback(Authentication::getId(), $TRIP_ID, $BODY)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function getFeedbacksByPassenger($DRIVER_ID)
    {
        Authentication::verifyJWT()
            ? $this->feedbackRepository->getFeedbacksByPassenger($DRIVER_ID)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function getFeedbacksToPassenger($PASSENGER_ID)
    {
        Authentication::verifyJWT()
            ? $this->feedbackRepository->getFeedbacksToPassenger($PASSENGER_ID)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }
}