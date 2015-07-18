---
layout: default
title: Getting Started
permalink: /usage/getting-started/
api: SessionInstance
---

You can store data using the set method:

~~~php
$session->set("customer-name", "Volition");

# Or if you are using the static class
Session::set("customer-name", "Volition");
~~~

And then you retrieve it using the get method:

~~~php
$name = $session->get("customer-name");

# Last example using the static class (all methods from the docs are available and named the same)
$name = Session::get("customer-name");
~~~

As we need to re-open the session everytime some data is set, it is better to set a bunch of values using an array:

~~~php
$session->set([
    "customer-name" =>  "Volition",
    "last-accessed" =>  time(),
    "location"      =>  "Skies",
]);

$location = $session->get("location");
~~~

-----

Keys that don't exist in the session are signified by `null`, this means that technically you cannot store a null value in session data:

~~~php
$session->set("an-actual-key", null);

$session->get("an-actual-key"); # null
$session->get("some-non-existant-key"); # null
~~~
