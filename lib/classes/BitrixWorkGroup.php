<?php

class BitrixWorkGroup {
    public function add_users(int $workgroup_id, array $users): ?array {
        $users_query = join(
            "&",
            array_map(function($user) {
                return "USER_ID[]=$user";
            }, $users)
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12305/kilpbbjrb94falye/sonet_group.user.add.json?GROUP_ID=$workgroup_id" . ($users_query ? "&$users_query" : ""));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }
    
    public function remove_users(int $workgroup_id, array $users): ?array {
        $users_query = join(
            "&",
            array_map(function($user) {
                return "USER_ID[]=$user";
            }, $users)
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12305/kilpbbjrb94falye/sonet_group.user.delete.json?GROUP_ID=$workgroup_id&$users_query");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }

    public function set_owner(int $workgroup_id, $bitrix_id): bool {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12305/a5xh2vgusjza8vm6/sonet_group.setowner.json?GROUP_ID=$workgroup_id&USER_ID=$bitrix_id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }

    public function create_workgroup(array $workgroup_options): ?int {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12305/f1wz00l3xg00pd1y/sonet_group.create.json?" . http_build_query([
            "NAME" => $workgroup_options["NAME"],
            "DESCRIPTION" => $workgroup_options["DESCRIPTION"],
            "KEYWORDS" => $workgroup_options["KEYWORDS"],
            "VISIBLE" => $workgroup_options["VISIBLE"],
            "OPENED" => $workgroup_options["OPENED"],
            "INITIATE_PERMS" => $workgroup_options["INITIATE_PERMS"],
            "SPAM_PERMS" => $workgroup_options["SPAM_PERMS"],
            "PROJECT" => $workgroup_options["PROJECT"],
            "PROJECT_DATE_START" => $workgroup_options["PROJECT_DATE_START"],
            "PROJECT_DATE_FINISH" => $workgroup_options["PROJECT_DATE_FINISH"]
        ], "", "&amp"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }

    public function get_workgroups_by(string $field, $value): ?array {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12305/4e8yzqi8k6oziqxf/sonet_group.get.json?FILTER[$field]=$value");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }

    public function delete_workgroup(int $group_id): bool {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://savecash.bitrix24.com.br/rest/12305/exstysbg9hk99fm1/sonet_group.delete.json?GROUP_ID=$group_id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["result"];
    }
}