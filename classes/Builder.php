<?php

namespace classes;

require_once('UpdateQueryBuilder.php');

class Builder
{
    public function getBuilderInstance($type)
    {
        switch ($type) {
            case 'insert':
                return new UpdateQueryBuilder;
                break;
            default:
                return false;
                break;
        }
    }
}
