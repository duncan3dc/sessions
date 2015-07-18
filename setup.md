---
layout: default
title: Setup
permalink: /setup/
api: SessionInterface
---

All classes are in the `duncan3dc\Sessions` namespace. You must provide a name for the session on instantiation.

~~~php
require_once __DIR__ . "vendor/autoload.php";

use duncan3dc\Sessions\SessionInstance;

$session = new SessionInstance("my-app");
~~~

There is also a static class available if you love [global state](//www.google.co.uk/search?q=global+state):

~~~php
use duncan3dc\Sessions\Session;

Session::name("my-app");
~~~

-----

You should type-hint using the `SessionInterface` rather than the concrete `SessionInstance` class.

~~~php
use duncan3dc\Sessions\SessionInterface;

function giveMeSessionData(SessionInterface $session)
~~~
