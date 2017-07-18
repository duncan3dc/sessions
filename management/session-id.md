---
layout: default
title: Session ID
permalink: /management/session-id/
api: SessionInstance
---

You can use specific session ID by pasing it as the third parameter when creating a `SessionInstance`:

~~~php
use duncan3dc\Sessions\SessionInstance;

$session = new SessionInstance("session-name", null, "session-id");
~~~

---

You can then retrieve the current session ID like so:

~~~php
$id = $session->getId();
~~~

---

<p class="message-info">These features were added in v1.2.0</p>
