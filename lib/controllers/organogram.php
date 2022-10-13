<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/SigaCandidate.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixUser.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixOrganogram.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";

class OrganogramController {
    public function update_user_in_tree(int $id){
        $siga_candidate = new SigaCandidate();
        $bitrix_organogram = new BitrixOrganogram();
        $bitrix_user = new BitrixUser();

        $superiors = $siga_candidate->get_superior_recursive($id, true);

        $bitrix_id = intval(
            get_post_meta($id, "c_bitrix_user_id", true)
        );
        $b_user = $bitrix_user->get_user($bitrix_id);
        $hierarquie = get_post_meta($id, "c_vaga", true);

        $profiles_name_match = strtolower($b_user["NAME"] ?? "") == strtolower(get_the_title($id));
    
        if (!$profiles_name_match || is_null($b_user)) {

            $email = get_post_meta($id, "c_email", true);
            $b_users = $bitrix_user->get_users_by_email($email);

            if ($b_users) {
                $bitrix_id = intval($b_users[0]["ID"]);
                update_post_meta($id, "c_bitrix_user_id", $bitrix_id);

            } else {

                $bitrix_id = $this->onBitrixUserNotFound($id);

                if ($bitrix_id) {
                    update_post_meta($id, "c_bitrix_user_id", $bitrix_id);
                } else {
                    throw new Error("Bitrix User creation fails after don't match", BI_ERROR_CODES["BITRIX_USER_NOT_FOUND"]);
                }

            }

        }

        if (!get_post_status($id)){
            $this->onCandidateNotFound($id);
            throw new Exception("Candidato relacionado ao ID $id não existe", BI_ERROR_CODES["CANDIDATE_NOT_EXISTS"]);
        }

        if (!$hierarquie) {
            throw new Exception("O candidato não possui uma vaga", BI_ERROR_CODES["HIERARQUIE_NOT_FOUND"]);
        }

        if ($hierarquie == "diretor"){
            $this->onCandidateIsDirector($id);
            throw new Exception("Diretores devem ser alterados manualmente", BI_ERROR_CODES["UPDATED_DIRECTOR"]);
        }

        if (!$superiors["diretor"]) {
            $this->onAssocDirectorNotFound($id);
            throw new Exception("Inconsistência de dados - Não existe um diretor associado á hierarquia desse candidato", BI_ERROR_CODES["HAVENT_ASSOC_DIRECTOR"]);
        }

        $department_association = [
            /* vaga */   /* departamento */
            "lider"      => "Líderes",
            "gerente"    => "Gerentes",
            "supervisor" => "Supervisores",
            "consultor"  => "Consultores",
        ];

        $target_department = $bitrix_organogram->get_departments_by_name_pattern(
            sprintf(
                "A%d - %s",
                $superiors["diretor"],
                $department_association[$hierarquie]
            )
        )[0];
                
        if (!$target_department) {
            $this->onTargetDepartmentNotFound($id, $bitrix_id, $hierarquie);

            throw new Exception("Department to assign candidate not found", BI_ERROR_CODES["TARGET_DEPARTMENT_NOT_FOUND"]);
        }

        $result = $bitrix_organogram->assign_to_departmament(
            $bitrix_id,
            intval($target_department["ID"]),
            $hierarquie
        );

        if ($result["result"] == true) {
            $this->onSuccess($id, $target_department["ID"]);
        }

        return $result;
    }
    public function onSuccess($id, $department){
        $path = WP_PLUGIN_DIR . "/bitrix-integrations/assets/sync_status.log";

        date_default_timezone_set('America/Sao_Paulo');
        $date = date("j/m - H:i");
        $log = "[$date]\tID [$id] OK | Departamento $department\n";

        file_put_contents($path, $log, FILE_APPEND);
    }

    public function onBitrixUserNotFound(int $id): int{
        require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixDeal.php";

        $fullname = ucwords(strtolower(get_the_title($id)));
        $fullname_array = explode(" ", trim($fullname));
        $first_name = $fullname_array[0];
        $last_name = join(" ", array_slice($fullname_array, 1));
        $email = get_post_meta($id, "c_email", true);
        $role = get_post_meta($id, "c_vaga", true);

        $bitrix_uuid = (new BitrixUser())->create_user(
            $first_name,
            $last_name,
            $email,
            $role,
            BI_ORGANOGRAM_DEFAULT_DEPARTMENT
        );

        if ($bitrix_uuid) {
            update_post_meta($id, "c_bitrix_user_id", $bitrix_uuid);

            (new BitrixDeal())->create_deal([
                "title" => $fullname,
                "assign_by_id" => $bitrix_uuid
            ]);
        }

        return intval($bitrix_uuid);
    }

    public function onCandidateNotFound(?int $id){
        $path = WP_PLUGIN_DIR . "/bitrix-integrations/assets/sync_status.log";

        date_default_timezone_set('America/Sao_Paulo');
        $date = date("j/m - H:i");
        $log = "[$date]\tcandidate with id [$id] not found\n";

        file_put_contents($path, $log, FILE_APPEND);
    }

    public function onAssocDirectorNotFound(?int $id){
        $path = WP_PLUGIN_DIR . "/bitrix-integrations/assets/sync_status.log";

        date_default_timezone_set('America/Sao_Paulo');
        $date = date("j/m - H:i");
        $log = "[$date]ID [$id] FAIL\n";

        file_put_contents($path, $log, FILE_APPEND);
    }

    private function onCandidateIsDirector($id){
        $path = WP_PLUGIN_DIR . "/bitrix-integrations/assets/sync_status.log";

        date_default_timezone_set('America/Sao_Paulo');
        $date = date("j/m - H:i");
        $log = "[$date]ID [$id] FAIL\n";

        file_put_contents($path, $log, FILE_APPEND);
    }

    private function onHierarquicalDepartmentsNotFound($id){
        $path = WP_PLUGIN_DIR . "/bitrix-integrations/assets/sync_status.log";

        date_default_timezone_set('America/Sao_Paulo');
        $date = date("j/m - H:i");
        $log = "[$date]ID [$id] FAIL\n";

        file_put_contents($path, $log, FILE_APPEND);
    }

    private function onTargetDepartmentNotFound($id, $bitrix_id, $hierarquie){
        $bitrix_organogram = new BitrixOrganogram();

        $bitrix_organogram->assign_to_departmament(
            $bitrix_id,
            BI_ORGANOGRAM_DEFAULT_DEPARTMENT,
            $hierarquie
        );

        $path = WP_PLUGIN_DIR . "/bitrix-integrations/assets/sync_status.log";

        date_default_timezone_set('America/Sao_Paulo');
        $date = date("j/m - H:i");
        $log = "[$date]ID [$id] FAIL\n";

        file_put_contents($path, $log, FILE_APPEND);
    }
}