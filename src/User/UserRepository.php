<?php

class UserRepository
{
    private PDO $connection;

    public function __construct()
    {
        $db = new DatabaseConfiguration();
        $this->connection = $db->connection();
    }

    public function newUser($BODY)
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO utente (nome ,cognome ,email ,telefono ,password ,carta_identita) VALUES(:nome ,:cognome ,:email ,:telefono ,:password ,:carta_identita)");
            $stmt->execute([
               "nome" => $BODY->nome,
               "cognome" => $BODY->cognome,
               "email" => $BODY->email,
               "telefono" => $BODY->telefono,
               "password" => Authentication::encrypt($BODY->password),
               "carta_identita" => $BODY->carta_identita
            ]);

            HTTP_Response::Send(HTTP_Response::MSG_CREATED, HTTP_Response::CREATED);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function loginUser($BODY)
    {
        try{
            $stmt = $this->connection->prepare("SELECT id FROM utente WHERE email = :email AND password = :password;");
            $stmt->execute([
                'email' => $BODY->email,
                'password' => Authentication::encrypt($BODY->password)
            ]);

            if($stmt->rowCount()) {
                HTTP_Response::SendWithBody(
                    HTTP_Response::MSG_OK,
                    array("JWT_TOKEN" => (new Authentication())->generateJWT($stmt->fetchAll())),
                    HTTP_Response::OK);
            }
            else{
                HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
            }
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function addLicense($ID, $BODY)
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO autista (id_utente, numero_patente, grado, rilasciato, scadenza) VALUES(:id_utente, :numero_patente, :grado, :rilasciato, :scadenza)");
            $stmt->execute([
                "id_utente" => $ID,
                "numero_patente" => Authentication::encrypt($BODY->numero_patente),
                "grado" => $BODY->grado,
                "rilasciato" => $BODY->rilasciato,
                "scadenza" => $BODY->scadenza
            ]);

            HTTP_Response::Send(HTTP_Response::MSG_CREATED, HTTP_Response::CREATED);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function addVehicle($ID, $BODY)
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO veicolo (targa, marca, modello, alimentazione, numero_posti, aria_condizionata, foto, id_autista) VALUES(:targa, :marca, :modello, :alimentazione, :numero_posti, :aria_condizionata, :foto, :id_autista)");
            $stmt->execute([
                "targa" => $BODY->targa,
                "marca" => $BODY->marca,
                "modello" => $BODY->modello,
                "alimentazione" => $BODY->alimentazione,
                "numero_posti" => $BODY->numero_posti,
                "aria_condizionata" => $BODY->aria_condizionata,
                "foto" => $BODY->foto,
                "id_autista" => $ID
            ]);

            HTTP_Response::Send(HTTP_Response::MSG_CREATED, HTTP_Response::CREATED);
        }
        catch (PDOException $exception){
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

}