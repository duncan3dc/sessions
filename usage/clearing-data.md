---
layout: default
title: Clearing Data
permalink: /usage/clearing-data/
---

You can unset individual keys like so:

~~~php
$session->delete("company-name");
~~~

You can also unset a bunch of keys at once (much faster than calling `delete()` in a loop):

~~~php
$session->delete("company-name", "location");
~~~

---

If you want to clear all of the current session data, you can do so:

~~~php
$session->clear();
~~~

Or you can just clear a particular namespace:

~~~php
$session->createNamespace("backend")->clear();
~~~

-----

Finally if you want to take down the whole session (typically only used when a user signs out), the destroy method is available:

~~~php
$session->destroy();
~~~
