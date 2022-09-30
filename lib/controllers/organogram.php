<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/SigaCandidate.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixOrganogram.php";

class OrganogramController {
    public function update_user_in_tree(int $id){
        $siga_candidate = new SigaCandidate();
        $bitrix_organogram = new BitrixOrganogram();

        $superiors = $siga_candidate->get_superior_recursive($id);

        $last_superior_hierarquie = get_post_meta(end($superiors), "c_vaga", true);

        if (get_post_status($id)){
            throw new Exception("Candidato relacionado ao ID $id não existe", 2);
        }
        
        if ($last_superior_hierarquie != "diretor") {
            throw new Exception("Inconsistência de dados - Não existe um diretor associado á hierarquia desse candidato", 2);
        } else if ($last_superior_hierarquie == "diretor"){
            throw new Exception("Diretores devem ser alterados manualmente", 3);
        }

        $assoc_director_departments = $bitrix_organogram->get_departments_by_name_pattern("A$id%");

        $hierarquical_departments = $bitrix_organogram->get_child_departments(
            $assoc_director_departments[0]["ID"]
        );

        if (in_array(array_column($hierarquical_departments, "NAME"),  ["Líderes", "Gerentes", "Supervisores", "Consultores"])) {
            throw new Exception("Inconsistência de dados - Não existem 4 departamentos hierarquicos para o diretor " . get_the_title(end($superiors)) . ". Faça o ajuste manualmente", 2);
        }

        $hierarquie = get_post_meta($id, "c_vaga", true);
        $department_association = [
            /* vaga */   /* departamento */
            "lider"      => "Líderes",
            "gerente"    => "Gerentes",
            "supervisor" => "Supervisores",
            "consultor"  => "Consultores",
        ];

        $department = array_filter(
            $hierarquical_departments,
            function($department) use ($department_association, $hierarquie){
                return $department["NAME"] == $department_association[$hierarquie];
            }
        )[0];

        $bitrix_id = intval(
            get_post_meta($id, "c_bitrix_user_id", true)
        );

        if (!$bitrix_id) {
            throw new Error("Bitrix User not found", 1);
        }

        if (!$department) {
            throw new Error("Department to assign candidate not found", 1);
        }

        return $bitrix_organogram->assign_to_departmament(
            $bitrix_id,
            $department["ID"],
            $hierarquie
        );
    }
}