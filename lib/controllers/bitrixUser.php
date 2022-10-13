<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixUser.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";

class bitrixUserController {
    public function create_user_and_deal(int $id): ?int{
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
}