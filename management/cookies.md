---
layout: default
title: Cookies
permalink: /management/cookies/
api: Cookie
---

The `Cookie` class can be used to manage the cookie settings for a session. It is [Immutable](https://en.wikipedia.org/wiki/Immutable_object).

Creating the class will use all the [default settings](http://php.net/manual/en/session.configuration.php) of PHP

~~~php
use duncan3dc\Sessions\Cookie;

$cookie = new Cookie;
~~~

---

You can create an instance using the current ini values like so:

~~~php
$cookie = Cookie::createFromIni();
~~~

---

The cookie instance can then be passed when creating a `SessionInstance`:

~~~php
$session = new Session("phpmyadmin", $cookie);
~~~

---

The API docs linked above show all the available methods.
