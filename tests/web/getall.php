<?php

require __DIR__ . "/bootstrap.php";

echo serialize($session->getAll());
