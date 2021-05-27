<?php


class FeedbackController extends Rest
{
    private FeedbackService $feedbackService;

    /**
     * FeedbackController constructor.
     * @param $REQUEST_METHOD
     * @param $PARAMS
     * @param $BODY
     */
    public function __construct($REQUEST_METHOD, $PARAMS, $BODY)
    {
        $this->feedbackService = new FeedbackService();
        parent::__construct($REQUEST_METHOD, $PARAMS, $BODY);
    }

    /**
     * @param $PARAMS
     * @param $BODY
     */
    function getMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case "all":
                $this->feedbackService->getAllFeedbacks();
                break;
            case "by_passenger":
                $this->feedbackService->getFeedbacksByPassenger($PARAMS[1]);
                break;
            case "to_passenger":
                $this->feedbackService->getFeedbacksToPassenger($PARAMS[1]);
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
                $this->feedbackService->addFeedback($PARAMS[1], $BODY);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }

}