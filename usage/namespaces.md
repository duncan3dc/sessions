---
layout: default
title: Namespaces
permalink: /usage/namespaces/
api: SessionNamespace
---

To avoid clashes within your application you can use namespaces within your session

~~~php
$session->set("user", "Mark");

$backend = $session->createNamespace("backend");
$backend->set("user", "Caroline");

$session->get("user"); # "Mark"
$backend->get("user"); # "Caroline"
~~~

You can also create sub-namespaces:

~~~php
$mainReports = $session->createNamespace("reports");

$admin = $session->createNamespace("admin");
$adminReports = $admin->createNamespace("reports");
~~~

<p class="message-info">The SessionNamespace class implements the SessionInterface</p>
