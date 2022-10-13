<?php

try {
    define("BI_REST_API_SECRET", "8e7abf96cef2d5a8");
    define("BI_REST_API_NAMESPACE", "bitrix");
    define("BI_REST_API_DEBUG", true);
    define("BI_ERROR_CODES", [
        "CANDIDATE_NOT_EXISTS" => 1,
        "HAVENT_ASSOC_DIRECTOR" => 2,
        "UPDATED_DIRECTOR" => 3,
        "BITRIX_USER_CREATION" => 4,
        "BITRIX_USER_NOT_FOUND" => 5,
        "HIERARQUICAL_DEPS_INCONSISTENCE" => 6,
        "TARGET_DEPARTMENT_NOT_FOUND" => 7,
        "BITRIX_USER_DONT_MATCH" => 8,
        "PLUGIN_CONSTANTS_FAIL" => 9,
        "HIERARQUIE_NOT_FOUND" => 10,
        "REQUEST_TO_NON_HIRED" => 11,
        "WORKGROUP_ALREADY_EXISTS" => 12,
        "WORKGROUP_CREATION_FAILS" => 13,
        "SET_WORKGROUP_OWNER" => 14,
        "WORKGROUP_DONT_EXISTS" => 15,
    ]);
    
    define("BI_SLUG_CANDIDATES", "c_consultores");
    
    define("BI_ORGANOGRAM_DEFAULT_DEPARTMENT", 32289);
} catch (\Throwable $e) {
    throw new Exception("Bitrix Integrations - não foi possível definir as contantes", BI_ERROR_CODES["PLUGIN_CONSTANTS_FAIL"]);
}