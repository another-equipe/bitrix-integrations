<?php

class BitrixUser {
    /**
     * Desativa um úsuario
     * @param int $bitrix_id ID do úsuario
    */
    public function deactivate_user(int $bitrix_id){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12291/cnqcgclu356flgr3/user.update.json?ID=$bitrix_id&ACTIVE=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }

}