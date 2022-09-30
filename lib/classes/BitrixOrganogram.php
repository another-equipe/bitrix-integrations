<?php

class BitrixOrganogram {
    /**
     * Muda o departamento de um úsuario
     * @param int $bitrix_id
     * @param int $department_id ID departamento que o úsuario irá
     * @param string $work_position Cargo do usuario no departamento
     * @return array|null  
    */
    public function assign_to_departmament(int $bitrix_id, int $department_id, string $work_position): ?array {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12291/cnqcgclu356flgr3/user.update.json?ID=$bitrix_id&UF_DEPARTMENT=$department_id&WORK_POSITION=$work_position");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }

    /**
     * Obtem departamentos filhos diretos de um departamento
     * @param int $department_id ID do departamento
     * @return array lista de departamentos
    */
    public function get_child_departments(int $department_id): ?array{
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/5/vsjaeeetcxavdtx9/department.get.json?filter[PARENT]=$department_id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }

    /**
     * Obtem os departamentos que tem um email especifico no final do nome do departamento
     * @param string $pattern nome do departament. Pode ser usado um pattern como o [SQL LIKE Statement](https://www.w3schools.com/sql/sql_like.asp)
     * @return array departamentos
    */
    public function get_departments_by_name_pattern(string $pattern): array {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/5/vsjaeeetcxavdtx9/department.get.json?filter[NAME]=$pattern");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }
}