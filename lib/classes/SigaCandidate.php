<?php

class SigaCandidate {
    public function get_superior_recursive(int $id, bool $assoc = false, int $max_hierarque = 5): ?array{
        global $wpdb;

        $hierarquie = [$id];

        while ($max_hierarque > 0) {
            $sql = "SELECT meta_value AS parent_id FROM wp_postmeta WHERE meta_key = 'c_c_parent' AND post_id = %d";
            $query = $wpdb->prepare($sql, end($hierarquie));
            $parent = $wpdb->get_results($query)[0]->parent_id;
    
            if ($parent) {
                if ($assoc){
                    $role = get_post_meta($parent, "c_vaga", true);
                    $hierarquie[$role] = intval($parent);
                } else {
                    array_push($hierarquie, intval($parent));
                }
            } else {
                break;
            }

            $max_hierarque--;
        }

        return $hierarquie;
    }

    public function get_superior(int $id): ?int{
        global $wpdb;

        $query = "SELECT meta_value AS parent_id FROM wp_postmeta WHERE meta_key = 'c_c_parent' AND post_id = $id";
        return $wpdb->get_results($query)[0]->parent_id;
    }

    public function get_imediate_childs(int $id): array {
        global $wpdb;

        $sql = "SELECT post_id FROM wp_postmeta WHERE meta_key = 'c_c_parent' AND meta_value = $id";
        $result = $wpdb->get_results($sql);
        $childs = array_column($result, "post_id");

        return $childs;
    }
    
}