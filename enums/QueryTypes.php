<?php
enum QueryTypes
{
    case ADD_RECORD_GET_ID;
    case ADD_RECORD;
    case SELECT_RECORD;
    case SELECT_MULTIPLE_RECORDS;
    case FIND_RECORD_EXISTENCE;
    case UPDATE_RECORD;
}
