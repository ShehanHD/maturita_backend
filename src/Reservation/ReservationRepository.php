<?php


class ReservationRepository
{
    private PDO $connection;

    /**
     * ReservationRepository constructor.
     */
    public function __construct()
    {
        $db = new DatabaseConfiguration();
        $this->connection = $db->connection();
    }

    public function getBookingByTripId($USER_ID, $TRIP_ID)
    {
        try{
            $stmt = $this->connection->prepare("SELECT * FROM prenotazione WHERE id_passeggero = :id_passeggero AND id_viaggio = :id_viaggio;");
            $stmt->execute([
                "id_passeggero" => $USER_ID,
                "id_viaggio" => $TRIP_ID
            ]);
            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllBookingParticipated($USER_ID)
    {
        try{
            $stmt = $this->connection->prepare("SELECT * FROM prenotazione WHERE id_passeggero = :id_passeggero;");
            $stmt->execute([
                "id_passeggero" => $USER_ID
            ]);
            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getBookingAllPassengersByTripId($USER_ID, $TRIP_ID, $VOTE)
    {
        try{
            if (!$this->isDriver($USER_ID, $TRIP_ID)) {
                HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST, ["error_msg" => "non sei la autista del viaggio"], HTTP_Response::BAD_REQUEST);
            }
            else{
                $stmt = $this->connection->prepare(
                    "SELECT *, AVG(f.voto) voto_media FROM prenotazione p
                            JOIN feedback f 
                                ON p.id_passeggero = f.id_passeggero
                                AND p.id_viaggio = :id_viaggio
                                AND f.da_chi = 'passeggero'
                                GROUP BY p.id_passeggero
                                HAVING AVG(f.voto) > :voto;"
                );
                $stmt->execute([
                    "id_viaggio" => $TRIP_ID,
                    "voto" => $VOTE
                ]);
                HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
            }
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function bookATrip($USER_ID, $TRIP_ID)
    {
        try{
            if ($this->isDriver($USER_ID, $TRIP_ID)) {
                HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST, ["error_msg" => "Autista del viaggio non può prenotare su di stesso"], HTTP_Response::BAD_REQUEST);
            }
            else{
                $stmt = $this->connection->prepare("INSERT INTO prenotazione (id_passeggero, id_viaggio)  VALUES(:id_passeggero, :id_viaggio);");
                $stmt->execute([
                    "id_passeggero" => $USER_ID,
                    "id_viaggio" => $TRIP_ID
                ]);
                HTTP_Response::Send(HTTP_Response::MSG_OK, HTTP_Response::OK);
            }
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function bookingState($USER_ID, $TRIP_ID, $BODY)
    {
        try{
            if (!$this->isDriver($USER_ID, $TRIP_ID)) {
                HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST, ["error_msg" => "Solo autista può cambiare stato della prenotazione"], HTTP_Response::BAD_REQUEST);
            }
            else {
                $stmt = $this->connection->prepare("UPDATE prenotazione SET stato = :stato WHERE id_viaggio = :id_viaggio;");
                $stmt->execute([
                    "id_viaggio" => $TRIP_ID,
                    "stato" => $BODY->state
                    ]);

                HTTP_Response::Send(HTTP_Response::MSG_OK, HTTP_Response::OK);
            }
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

}