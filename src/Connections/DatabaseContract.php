<?php

namespace Aiiro\Factory\Connections;

interface DatabaseContract
{
    public function fetchColumns($table);
}
