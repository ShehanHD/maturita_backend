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
            $this->connection->beginTransaction();

            $stmt = $this->connection->prepare("INSERT INTO patente (numero_patente, grado, rilasciato, scadenza) VALUES (:numero_patente, :grado, :rilasciato, :scadenza);");
            $stmt->execute([
                'numero_patente' => $BODY->numero_patente,
                'grado' => $BODY->grado,
                'rilasciato' => $BODY->rilasciato,
                'scadenza' => $BODY->scadenza
            ]);

            $stmt = $this->connection->prepare("INSERT INTO utente (nome, cognome, email, password, telefono, carta_identita, id_patente) VALUES (:nome, :cognome, :email, :password, :telefono, :carta_identita, :id_patente)");
            $stmt->execute([
                'nome' => $BODY->nome,
                'cognome' => $BODY->cognome,
                'email' => $BODY->email,
                'password' => Authentication::encrypt($BODY->password),
                'telefono' => $BODY->telefono,
                'carta_identita' => $BODY->carta_identita,
                'id_patente' => $BODY->numero_patente
            ]);

            $this->connection->commit();

            HTTP_Response::Send(HTTP_Response::MSG_CREATED, HTTP_Response::CREATED);
        }
        catch (PDOException $exception){
            $this->connection->rollBack();
            HTTP_Response::SendWithBody(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, $exception, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

    public function loginUser($BODY)
    {
        try{
            $stmt = $this->connection->prepare("SELECT email, password FROM utente WHERE email = :email AND password = :password;");
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

    public function addIdentity($id, $BODY)
    {
        try{
            $stmt = $this->connection->prepare("UPDATE utente SET carta_identita = :carta_identita WHERE id = :id;");
            $stmt->execute([
                'id' => $id,
                'carta_identita' => $BODY->carta_identita
            ]);
        }
        catch (PDOException $exception){

        }
    }

    public function addLicense($id, $BODY)
    {
        try{
            $this->connection->beginTransaction();

            $stmt = $this->connection->prepare("INSERT INTO patente (numero_patente, grado, rilasciato, scadenza) VALUES (:numero_patente, :grado, :rilasciato, :scadenza);");
            $stmt->execute([
                'numero_patente' => $BODY->numero_patente,
                'grado' => $BODY->grado,
                'rilasciato' => $BODY->rilasciato,
                'scadenza' => $BODY->scadenza
            ]);

            $stmt = $this->connection->prepare("UPDATE utente SET id_patente = :id_patente WHERE id = :id;");
            $stmt->execute([
                'id' => $id,
                'id_patente' => $BODY->numero_patente
            ]);

            $this->connection->commit();
        }
        catch (PDOException $exception){
            $this->connection->rollBack();
            HTTP_Response::Send(HTTP_Response::MSG_INTERNAL_SERVER_ERROR, HTTP_Response::INTERNAL_SERVER_ERROR);
        }
    }

}