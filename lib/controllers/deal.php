<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixDeal.php";

class DealController {
    public function create_deal(int $id){
        $BitrixDeal = new BitrixDeal();

        $fullname = ucwords(strtolower(get_the_title($id)));
        $bitrix_id = intval(
            get_post_meta($id, "c_bitrix_user_id", true)
        );

        $BitrixDeal->create_deal([
            "title" => $fullname,
            "assign_by_id" => $bitrix_id
        ]);
    }
}