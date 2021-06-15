<?php


class TripRepository
{
    private PDO $connection;

    /**
     * TripRepository constructor.
     */
    public function __construct()
    {
        $db = new DatabaseConfiguration();
        $this->connection = $db->connection();
    }
//AND data_di_partenza > NOW()
    public function getAllAvailable()
    {
        try{
            $stmt = $this->connection->prepare(
                "SELECT id, partenza, destinazione, durata, data_di_partenza, creato_al, contributo, v.foto, v.numero_posti
                        FROM viaggio 
                        JOIN veicolo v on v.targa = viaggio.id_veicolo and v.id_autista = viaggio.id_autista
                        WHERE stato = 'Not Completed'
                        AND data_di_partenza > NOW()
                        ORDER BY data_di_partenza;");
            $stmt->execute();
            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllDriven($ID)
    {
        try{
            $stmt = $this->connection->prepare(
                "SELECT id, id_veicolo, partenza, destinazione, durata, data_di_partenza, creato_al, stato, foto 
                FROM viaggio 
                JOIN veicolo v 
                    ON v.targa = viaggio.id_veicolo 
                           AND v.id_autista = viaggio.id_autista
                           AND v.id_autista = :id_autista;");
            $stmt->execute(["id_autista" => $ID]);
            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllParticipated($ID)
    {
        try{
            $stmt = $this->connection->prepare("SELECT * FROM viaggio v JOIN prenotazione p WHERE v.stato = 'Completed' AND p.id_passeggero = :id_passeggero;");
            $stmt->execute(["id_passeggero" => $ID]);
            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllFiltered($departure, $destination, $when)
    {
        try{
            $stmt = $this->connection->prepare(
                "SELECT 
                        v.id, v.partenza, v.destinazione, v.durata, v.creato_al, v.data_di_partenza, v.animali, v.bagagli, v.soste, v.contributo, 
                        v2.marca, v2.modello, v2.targa, v2.foto, v2.numero_posti
                        FROM viaggio v 
                        JOIN veicolo v2 ON v2.targa = v.id_veicolo and v2.id_autista = v.id_autista
                                             AND v.stato = 'Not Completed'
                                             AND UPPER(v.partenza) = UPPER(:partenza) 
                                             AND UPPER(v.destinazione) = UPPER(:destinazione) 
                                             AND DATE(v.data_di_partenza) = DATE(:data_di_partenza)
                        ORDER BY TIME(v.data_di_partenza);
                        ");
            $stmt->execute([
                "partenza" => "$departure",
                "destinazione" => "$destination",
                "data_di_partenza" => $when
            ]);
            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getTripById($ID)
    {
        try{
            $stmt = $this->connection->prepare(
                "SELECT
                    v.id, v.id_autista, v.partenza, v.destinazione, v.durata, v.creato_al, v.data_di_partenza, v.contributo, v.animali, v.bagagli, v.soste, 
                    u.nome, u.cognome, u.email, u.telefono,
                    v2.targa, marca, modello, alimentazione, numero_posti, aria_condizionata, foto
                FROM viaggio v
                JOIN veicolo v2 on v2.targa = v.id_veicolo and v2.id_autista = v.id_autista
                JOIN utente u
                ON v.id = :id 
                  AND v.stato = 'Not Completed'
                  AND v.id_autista = u.id
            ;");
            $stmt->execute(["id" => $ID]);
            $stmt->rowCount()
                ? HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll() , HTTP_Response::OK)
                : HTTP_Response::Send(HTTP_Response::MSG_NO_CONTENT, HTTP_Response::NO_CONTENT);

        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function getVehicleByDriver($ID)
    {
        try{
            $stmt = $this->connection->prepare("SELECT * FROM veicolo WHERE id_autista = :id_autista;");
            $stmt->execute(["id_autista" => $ID]);
            HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, $stmt->fetchAll(), HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function newTrip($ID, $BODY)
    {
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO viaggio 
                        (id, id_autista, id_veicolo, partenza, destinazione, durata, data_di_partenza, contributo, bagagli, soste, animali) 
                        VALUES
                        (:id, :id_autista, :id_veicolo, :partenza, :destinazione, :durata, :data_di_partenza, :contributo, :bagagli, :soste, :animali);
                        ");

            $stmt->execute([
                "id" => $this->tripId(),
                "id_autista" => $ID,
                "id_veicolo" => $BODY->id_veicolo,
                "partenza" => $BODY->partenza,
                "destinazione" => $BODY->destinazione,
                "durata" => $BODY->durata,
                "data_di_partenza" => $BODY->data_di_partenza,
                "contributo" => $BODY->contributo,
                "bagagli" => $BODY->bagagli,
                "soste" => $BODY->soste,
                "animali" => $BODY->animali
            ]);

            HTTP_Response::Send(HTTP_Response::MSG_CREATED, HTTP_Response::CREATED);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function closeTrip($ID)
    {
        try{
            $stmt = $this->connection->prepare("UPDATE viaggio SET stato = 'Completed' WHERE id = :id;");
            $stmt->execute(["id" => $ID]);
            HTTP_Response::Send(HTTP_Response::MSG_OK, HTTP_Response::OK);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function isDriver($USER_ID)
    {
        $stmt = $this->connection->prepare("SELECT * FROM autista WHERE id_utente = :id");
        $stmt->execute([
            "id" => $USER_ID
        ]);

        HTTP_Response::SendWithBody(HTTP_Response::MSG_OK, (bool)$stmt->rowCount(), HTTP_Response::OK);
    }

    private function tripId(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        $valid = 0;

        while (!$valid) {
            for ($i = 0; $i < 8; $i++) {
                $string .= $characters[mt_rand(0, strlen($characters) - 1)];
            }

            $stmt = $this->connection->prepare("SELECT id FROM viaggio WHERE id = :id;");
            $stmt->execute([
                'id' => $string
            ]);

            $valid = !$stmt->rowCount();
        }

        return $string;
    }

}