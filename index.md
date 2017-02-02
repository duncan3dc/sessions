---
layout: default
title: Sessions
permalink: /
---

[![Author](http://img.shields.io/badge/author-@duncan3dc-blue.svg?style=flat)](https://twitter.com/duncan3dc)
[![Source](http://img.shields.io/badge/source-duncan3dc/sessions-blue.svg?style=flat)](https://github.com/duncan3dc/sessions)
[![Latest Version](https://img.shields.io/packagist/v/duncan3dc/sessions.svg?style=flat)](https://packagist.org/packages/duncan3dc/sessions)
<br>
[![Software License](https://img.shields.io/badge/license-Apache--2.0-brightgreen.svg?style=flat)](https://github.com/duncan3dc/sessions/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/duncan3dc/sessions.svg?style=flat)](https://travis-ci.org/duncan3dc/sessions)
[![Issue Tracker](https://img.shields.io/github/issues/duncan3dc/sessions.svg?style=flat)](https://github.com/duncan3dc/sessions/issues)

A non-blocking session manager for PHP.

The main feature of this library is to prevent a user from waiting for a page to load because they have a previous long running request locking the session.

In addition to this the library can also manage namespaced portions of session data (to avoid common clashes) and a flash feature to store data until it is retrieved and then discard it.

<br>
<p class="message-api">Full <a href='{{ site.baseurl }}/api/namespaces/duncan3dc.Sessions.html'>API documentation</a> is also available.</p>
