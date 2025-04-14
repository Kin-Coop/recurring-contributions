<?php

namespace Civi\Api4;

//namespace Civi\Api4\Custom;

//use Civi\Api4\Generic\AbstractEntity;
//use Civi\Api4\Generic\DAOCreateAction;
//use Civi\Api4\Generic\DAOGetAction;

class Kinrc extends Generic\AbstractEntity
{
    /*
        protected function getActions()
        {
            return [
                //'copy' => CopyContributionAction::class,
            ];
        }
    */
    public static function getFields($checkPermissions = TRUE)
    {
        return (new Generic\BasicGetFieldsAction(__CLASS__, __FUNCTION__, function ($getFieldsAction) {
            return [
                [
                    'name' => 'id',
                    'data_type' => 'Integer',
                    'description' => 'Unique identifier. If it were named something other than "id" we would need to override the getInfo() function to supply "primary_key".',
                ],
                [
                    'name' => 'example_str',
                    'description' => "Example string field. We don't need to specify data_type as String is the default.",
                ],
                [
                    'name' => 'example_int',
                    'data_type' => 'Integer',
                    'description' => "Example number field. The Api Explorer will present this as numeric input.",
                ],
                [
                    'name' => 'example_bool',
                    'data_type' => 'Boolean',
                    'description' => "Example boolean field. The Api Explorer will present true/false options.",
                ],
                [
                    'name' => 'example_options',
                    'description' => "Example field with option list. The Api Explorer will display these options.",
                    'options' => ['r' => 'Red', 'b' => 'Blue', 'g' => 'Green'],
                ],
            ];
        }))->setCheckPermissions($checkPermissions);
    }

    public static function create($checkPermissions = TRUE) {
        return (new Action\Kinrc\Create(__CLASS__, __FUNCTION__))
            ->setCheckPermissions($checkPermissions);
    }
}
