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

    public function getBookingAllPassengersByTripId($USER_ID, $TRIP_ID, $VOTE)
    {
        try {
            if (!$this->isDriver($USER_ID, $TRIP_ID)) {
                HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST, ["error_msg" => "non sei la autista del viaggio"], HTTP_Response::BAD_REQUEST);
            } else {
                $stmt = $this->connection->prepare(
                    "SELECT *, AVG(f.voto) voto_media FROM prenotazione p
                            LEFT JOIN feedback f 
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
        } catch (PDOException $exception) {
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function bookATrip($USER_ID, $TRIP_ID)
    {
        try {
            if ($this->isDriver($USER_ID, $TRIP_ID)) {
                HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST, ["error_msg" => "Autista del viaggio non può prenotare su di stesso"], HTTP_Response::BAD_REQUEST);
            } else {
                $stmt = $this->connection->prepare("INSERT INTO prenotazione (id_passeggero, id_viaggio)  VALUES(:id_passeggero, :id_viaggio);");
                $stmt->execute([
                    "id_passeggero" => $USER_ID,
                    "id_viaggio" => $TRIP_ID
                ]);

                if ($stmt->rowCount()) {
                    $data = sendMail("new", $this->getTripData($TRIP_ID), $this->getFeedbacksToPassenger($USER_ID));
                    HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $data, HTTP_Response::OK);
                } else {
                    HTTP_Response::Send(HTTP_Response::MSG_NOT_ACCEPTABLE, HTTP_Response::NOT_ACCEPTABLE);
                }
            }
        } catch (PDOException $exception) {
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function bookingState($USER_ID, $TRIP_ID, $BODY)
    {
        try {
            if (!$this->isDriver($USER_ID, $TRIP_ID)) {
                HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST, ["error_msg" => "Solo autista può cambiare stato della prenotazione"], HTTP_Response::BAD_REQUEST);
            } else {
                $stmt = $this->connection->prepare("UPDATE prenotazione SET stato = :stato WHERE id_viaggio = :id_viaggio AND id_passeggero = :id_passeggero;");
                $stmt->execute([
                    "id_viaggio" => $TRIP_ID,
                    "id_passeggero" => $BODY->id_passeggero,
                    "stato" => $BODY->state
                ]);

                if ($stmt->rowCount()) {
                    sendMail($BODY->state, $this->getTripData($TRIP_ID), null);
                    HTTP_Response::Send(HTTP_Response::MSG_OK, HTTP_Response::OK);
                } else {
                    HTTP_Response::Send(HTTP_Response::MSG_NOT_ACCEPTABLE, HTTP_Response::NOT_ACCEPTABLE);
                }
            }
        } catch (PDOException $exception) {
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    private function isDriver($USER_ID, $TRIP_ID): bool
    {
        $stmt = $this->connection->prepare("SELECT * FROM viaggio WHERE id = :id AND id_autista = :id_autista;");
        $stmt->execute([
            "id" => $TRIP_ID,
            "id_autista" => $USER_ID
        ]);

        return (bool)$stmt->rowCount();
    }

    private function getTripData($TRIP_ID)
    {
        try {
            $stmt = $this->connection->prepare(
                "SELECT 
                            v.id, id_autista, id_veicolo, partenza, destinazione, durata, data_di_partenza, contributo, stato, bagagli, soste, animali,
                            nome, cognome, email, password, telefono
                            FROM viaggio v 
                            JOIN utente u 
                                ON u.id = v.id_autista
                                AND v.id = :id_viaggio;
                       "
            );
            $stmt->execute([
                "id_viaggio" => $TRIP_ID
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $exception) {
            return 0;
        }
    }

    private function getFeedbacksToPassenger($PASSENGER_ID)
    {
        try {
            $stmt = $this->connection->prepare(
                "SELECT u.nome, u.cognome, u.email, u.telefono, f.giudizio, f.voto
                    FROM feedback f
                    JOIN utente u ON f.id_autista = u.id
                            AND f.da_chi = 'autista';"
            );
            $stmt->execute(["id" => $PASSENGER_ID]);

            return $stmt->fetchAll();
        } catch (PDOException $exception) {
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }
}
