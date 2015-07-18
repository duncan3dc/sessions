---
layout: default
title: Get/Set
permalink: /usage/get-set/
---

To avoid having to keep checking `$_POST` or `$_GET` data and falling back to session data or a default value, there is a convenience method available:

~~~php
$option = $session->getSet("dropdown-option", "main-menu");
~~~

This method will do the following:  
* If `$_POST["dropdown-option"]` exists it will store it in the session and return it.  
* If `$_GET["dropdown-option"]` exists it will store it in the session and return it.  
* If `$session->get("dropdown-option")` exists it will return it.  
* It will return "main-menu".  
