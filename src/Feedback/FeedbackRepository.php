<?php


class FeedbackRepository
{
    private PDO $connection;

    /**
     * FeedbackRepository constructor.
     */
    public function __construct()
    {
        $db = new DatabaseConfiguration();
        $this->connection = $db->connection();
    }

    public function addFeedback($USER_ID, $TRIP_ID, $BODY)
    {
        try{
            if($this->isTripClosed($TRIP_ID)) {
                $from = $this->isDriver($USER_ID, $TRIP_ID) ? "autista" : "passeggero";

                $stmt = $this->connection->prepare("INSERT INTO feedback
                            (da_chi, giudizio, voto, id_viaggio, id_autista, id_passeggero)  
                            VALUES(:da_chi, :giudizio, :voto, :id_viaggio, :id_autista, :id_passeggero);");
                $stmt->execute([
                    "da_chi" => $from,
                    "giudizio" => $BODY->giudizio,
                    "voto" => $BODY->voto,
                    "id_viaggio" => $TRIP_ID,
                    "id_autista" => $BODY->id_autista,
                    "id_passeggero" => $BODY->id_passeggero,
                ]);

                HTTP_Response::Send(HTTP_Response::MSG_OK, HTTP_Response::OK);
            }
            else{
                HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST, ["error_msg" => "Il viaggio ancora non è completato!"], HTTP_Response::BAD_REQUEST);
            }
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllFeedbacks($USER_ID)
    {
        try{
            $stmt = $this->connection->prepare("SELECT * FROM feedback WHERE id_autista = :id OR id_passeggero = :id");
            $stmt->execute(["id" => $USER_ID]);

            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getFeedbacksByPassenger($DRIVER_ID)
    {
        try{
            $stmt = $this->connection->prepare(
                "SELECT f.id, f.voto, f.giudizio, u.nome, u.cognome, f.id_viaggio
                        FROM feedback f
                        JOIN utente u on f.id_passeggero = u.id
                        WHERE id_autista = :id 
                        AND da_chi = 'passeggero';"
            );
            $stmt->execute(["id" => $DRIVER_ID]);

            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getFeedbacksToPassenger($PASSENGER_ID)
    {
        try{
            $stmt = $this->connection->prepare(
                "SELECT * FROM feedback 
                        WHERE id_passeggero = :id 
                          AND da_chi = 'autista'");
            $stmt->execute(["id" => $PASSENGER_ID]);

            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    private function isDriver($USER_ID, $TRIP_ID): bool
    {
        $stmt = $this->connection->prepare("SELECT * FROM viaggio WHERE id = :id AND id_autista = :id_autista;");
        $stmt->execute([
            "id"=> $TRIP_ID,
            "id_autista" => $USER_ID
        ]);

        return (bool)$stmt->rowCount();
    }

    private function isTripClosed($TRIP_ID): bool
    {
        $stmt = $this->connection->prepare("SELECT * FROM viaggio WHERE id = :id AND stato = 'Completed'");
        $stmt->execute([
            "id"=> $TRIP_ID
        ]);

        return (bool)$stmt->rowCount();
    }

}