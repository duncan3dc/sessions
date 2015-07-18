---
layout: default
title: Flash Data
permalink: /usage/flash-data/
---

This library supports storing one-time use data, commonly referred to as "flash messages" or "flash data".  
This is useful to store some data from a request, and then display it in the next response:

~~~php
$result = $db->updateRecord(5);
if ($result) {
    $session->setFlash("message", "The record was updated successfully");
} else {
    $session->setFlash("message", "An error has occurred");
}
~~~

~~~php
if ($message = $session->getFlash("message")) {
    echo "<div class='message'>{$message}</div>";
}
~~~

And then the flash message is gone and further calls to `getFlash()` will return null.

-----

Flash data can co-exist with regular session data:

~~~php
$session->set("message", "Hello");
$session->setFlash("message", "Flash Message");

$session->get("message"); # Hello
$session->getFlash("message"); # Flash Message
~~~
