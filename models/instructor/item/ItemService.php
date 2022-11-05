<?php

require_once('./models/instructor/item/ItemRepository.php');
require_once('./models/instructor/item/ItemTemplate.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');

class ItemService implements ItemTemplate
{
    private ItemRepository $item_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->validator = new Validation();
        $this->item_repository = new ItemRepository();
    }

    public function add_item($item_data)
    {
        if ($this->item_repository->add_item($item_data)) {
            return response(['message' => 'Item added.'], 200);
        }
    }

    public function get_item($item_id)
    {
        return response($this->item_repository->get_item($item_id), 200);
    }

    public function get_items($activity_id)
    {
        return response($this->item_repository->get_items($activity_id), 200);
    }

    public function update_item($item_data)
    {
        if ($this->item_repository->update_item($item_data)) {
            return response(["message" => "Item updated."], 200);
        } else {
            return response(["message" => "Nothing to update."], 400);
        }
    }

    public function delete_item($item_id)
    {
        if ($this->item_repository->delete_item($item_id)) {
            return response(["message" => "Item deleted."], 200);
        } else {
            return response(["message" => "Resource not found."], 404);
        }
        
    }

}
