<?php


namespace App\BLL;


use App\Entity\Tablet;
use App\Entity\Usuario;

class OneSignalBLL extends BaseBLL
{
    protected const APPLICATION_ID = '';
    protected const APPLICATION_AUTH_KEY = '';

    public function getAllDevices()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players?app_id=" . self::APPLICATION_ID);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
            'Authorization: Basic ' . self::APPLICATION_AUTH_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function newDevice(array $data)
    {
        $campos = [
            'app_id' => self::APPLICATION_ID,
            'device_type' => '1',
            'identifier' => $data['idDevice'],
            'language' => 'es'
        ];

        $this->newTablet($data['idDevice']);

        $campos = json_encode($campos);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $campos);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);


        return $response;
    }

    public function sendPushDevice(array $data,array $listadoAnuncios)
    {
        $content = array(
            "en" => 'Iniciar Servicio'
        );
        $campos = array(

            'app_id' => self::APPLICATION_ID,
            'include_player_ids' => [$data['idDevice']],
            'data' => ['anuncios' => $listadoAnuncios['anuncios'], 'idServicio' => $listadoAnuncios['idServicio'],'waitTime' => $listadoAnuncios['waitTime']],
            'contents' => $content
        );

        $campos = json_encode($campos);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $campos);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function newTablet(string $IdOneSignal): void
    {
        $tablet = new Tablet();
        $usuarioId = $this->tokenStorage->getToken()->getUser()->getUsername();
        $usuarioId = $this->entityManager->getRepository(Usuario::class)->findOneBy(['username' => $usuarioId]);

        $tablet->setIdOneSignal($IdOneSignal);
        $tablet->setIdUsuario($usuarioId);
        $this->entityManager->persist($tablet);
        $this->entityManager->flush();
    }
}