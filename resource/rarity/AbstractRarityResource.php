<?php

require_once 'vendor/autoload.php';

/**
 * Abstract super class for all resources based on the {@link Rarity} enity.
 */
class AbstractRarityResource extends AbstractResource {

    protected function validate($damageType) {
        $errors = array();
        if (empty($damageType["name"])) {
            $errors["name"] = "Name is required";
        }
        if (empty($damageType["color"])) {
            $errors["color"] = "Color is required";
        }
        if (empty($damageType["sortOrder"])) {
            $errors["sortOrder"] = "Sort order is required";
        }
        return $errors;
    }

}
