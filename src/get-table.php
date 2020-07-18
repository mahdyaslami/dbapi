<?php

global $db;

jsonResult(200, null, null, $db->select($table, '*'));
