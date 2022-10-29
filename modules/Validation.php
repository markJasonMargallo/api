<?php
require_once('./modules/Procedural.php');
require_once('./vendor/autoload.php');
require_once('./models/exception/BadRequestException.php');


use JsonSchema\Validator;

class Validation{

    private Validator $validator;

    public function __construct()
    {
        $this->validator = new JsonSchema\Validator;
    }

    public function is_body_valid($json_to_validate, string $path_to_schema){

        $this->validator->validate($json_to_validate, (object)['$ref' => 'file://' . realpath($path_to_schema)]);
        if ($this->validator->isValid()) {
            return true;
        }
        return false;
    }

    public function invalid_body(){
        $errors = $this->validator->getErrors()[0];
        $error_property = $errors['property'];
        $error_message = $errors['message'];

        throw new BadRequestException("$error_property : $error_message");
        // throw new BadRequestException();
    }
}