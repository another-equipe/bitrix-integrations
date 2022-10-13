<?php

class BitrixUser {
    /**
     * Desativa um úsuario
     * @param int $bitrix_id ID do úsuario
    */
    public function deactivate_user(int $bitrix_id) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12291/cnqcgclu356flgr3/user.update.json?ID=$bitrix_id&ACTIVE=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }

    public function create_user(string $first_name, string $last_name, string $email, string $role, int $department): ?int {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/5/qdolajv5wj7s15su/user.add.json?ACTIVE=true&NAME=$first_name&LAST_NAME=$last_name&EMAIL=$email&WORK_POSITION=$role&UF_DEPARTMENT=$department");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        if (array_key_exists("error", $response)) {
            return null;
        }

        return $response["result"];
    }

    public function get_user(int $bitrix_id): ?array {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12291/2rugktp4u252wwtg/user.get.json?ID=$bitrix_id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["total"] > 0 ? $response["result"][0] : null;
    }

    public function get_users_by_email(string $email): ?array {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12291/2rugktp4u252wwtg/user.get.json?filter[EMAIL]=$email");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["total"] > 0 ? $response["result"] : null;
    }

}