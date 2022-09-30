<?php

class BitrixWorkGroup {
    public function invite(int $group_id, array $user_id, string $invitation_message){
        return ["result" => $user_id];
    }
}