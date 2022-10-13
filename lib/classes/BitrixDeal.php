<?php

class BitrixDeal {
    public function create_deal(array $deal) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://savecash.bitrix24.com.br/rest/5/3afdy25dz6672b50/crm.deal.add.json",
            CURLOPT_POSTFIELDS => http_build_query([
                'fields' => [
                    "TITLE"                 => $deal["title"],
                    "CATEGORY_ID"           => $deal["category_id"] ?? 17,
                    "STAGE_ID"              => $deal["stage_id"] ?? "C17:NEW",
                    "OPENED"                => $deal["opened"] ?? "Y",
                    "ASSIGNED_BY_ID"        => $deal["assign_by_id"],
                    "TYPE_ID"               => $deal["type_id"] ?? "GOODS",
                    "PROBABILITY"           => $deal["probability"] ?? "",
                    "BEGINDATE"             => $deal["begindate"] ?? date('Y-m-d H:i:s'),
                    "CLOSEDATE"             => $deal["closedate"] ?? date('Y-m-d H:i:s'),
                    "OPPORTUNITY"           => $deal["opportunity"] ?? 0,
                    "CURRENCY_ID"           => $deal["currency_id"] ?? "BRL",
                    "UF_CRM_1650365006867"  => $deal["uf_crm_code"] ?? "https://academia.savecash.com.br",
                    "COMMENTS"              => $deal["comments"] ?? "Comentário"
                ],
                'params' => [
                    "REGISTER_SONET_EVENT" => $deal["register_sonet_event"] ?? "N"
                ]
            ]),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }

}